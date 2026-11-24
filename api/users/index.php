<?php

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";
require_once __DIR__ . "/../../includes/json_functions.php";
require_once __DIR__ . "/user_functions.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$usersIndex = array_search('users', $parts, true);
$routePart = $usersIndex !== false ? ($parts[$usersIndex + 1] ?? null) : null;
$subResource = $usersIndex !== false ? ($parts[$usersIndex + 2] ?? null) : null;


/*
------------------------------------------------------------
After successfully changing your password
------------------------------------------------------------
*/
if ($routePart === 'reset-password') {
    if ($method !== 'POST') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }

    $data = getJsonBody();
    $email = trim((string)($data['email'] ?? ''));
    $newPassword = (string)($data['password'] ?? '');

    // Makes sure email is valid
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(400, ['success' => false, 'message' => 'Please enter a valid email address.']);
    }

    // Makes sure password is 8 characters long
    if (strlen($newPassword) < 8) {
        sendJson(400, ['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    }

    // Checks to find user
    $sql = 'SELECT userid FROM users WHERE email = :email LIMIT 1';
    $users = WFDatabase::getDataFromSQL($sql, [':email' => $email]);

    $user = $users[0] ?? null;

    if (!$user) {
        sendJson(404, ['success' => false, 'message' => 'No user found with that email address.']);
    }

    // Updates the password in the database
    $updated_password = 'UPDATE users SET password = :password WHERE userid = :userid';

    WFDatabase::executeSQL($updated_password, [
        ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ':userid' => (int)$user['userid']
    ]);

    sendJson(200, ['success' => true, 'message' => 'Password updated successfully.']);
}


/*
------------------------------------------------------------
POST /api/users/register
------------------------------------------------------------
*/
if ($routePart === 'register') {
    if ($method !== 'POST') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }

    $data = getJsonBody();
    $email = trim((string)($data['email'] ?? ''));
    $userpassword = (string)($data['password'] ?? '');

    // Real email address first, then Etown only
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(400, ['success' => false, 'message' => 'A valid email address is required.']);
    }

    if (!isValidEtownEmail($email)) {
        sendJson(400, ['success' => false, 'message' => 'Only @etown.edu emails can register.']);
    }

    if (strlen($userpassword) < 8) {
        sendJson(400, ['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    }

    $check_sql = 'SELECT userid FROM users WHERE email = :email';
    $check_params = [':email' => $email];

    $user_already_exist_results = WFDatabase::getDataFromSQL($check_sql, $check_params);

    if (count($user_already_exist_results) > 0) {
        sendJson(409, ['success' => false, 'message' => 'That email address is already registered.']);
    }
    #TODO: Should validate the country id
    $countryId = $data['original_country_id'];

    $insert_user_sql = "INSERT INTO users
    (email, first_name, last_name, date_registered, original_country_id, password)
    VALUES
    (:email, :first_name, :last_name, :date_registered, :original_country_id, :password)";


    $insert_params = [
        ':email' => $email,
        ':first_name' => isset($data['first_name']) && trim((string)$data['first_name']) !== '' ? trim((string)$data['first_name']) : null,
        ':last_name' => isset($data['last_name']) && trim((string)$data['last_name']) !== '' ? trim((string)$data['last_name']) : null,
        ':date_registered' => date('Y-m-d H:i:s'),
        ':original_country_id' => $countryId,
        ':password' => password_hash($userpassword, PASSWORD_DEFAULT),
    ];

    $userId = WFDatabase::executeSQL($insert_user_sql, $insert_params, true);

    // Save the language they picked during registration
    $languageId = (int)($data['language_id'] ?? 0);
    if ($languageId > 0) {
        WFDatabase::executeSQL(
            "INSERT INTO user_spoken_languages (userid, language_id, proficency_level)
             VALUES (:userid, :lang, 5)",
            [':userid' => $userId, ':lang' => $languageId]
        );
    }

    createEmailVerification((int)$userId, $email);

    $user_data = getUserById($userId);

    sendJson(201, [
        'success' => true,
        'message' => 'Account created! Check your Etown email for a verification link.',
        'user' => $user_data,
    ]);
}

/*
------------------------------------------------------------
POST /api/users/login
------------------------------------------------------------
*/
if ($routePart === 'login') {
    if ($method !== 'POST') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }

    $data = getJsonBody();
    $email = trim((string)($data['email'] ?? ''));
    $userpassword = (string)($data['password'] ?? '');

    $user_login_sql = 'SELECT userid, password, email_verified FROM users WHERE email = :email LIMIT 1';
    $login_params = [':email' => $email];
    $records = WFDatabase::getDataFromSQL($user_login_sql, $login_params);
    $record = $records[0] ?? null;
    if ((int)$record['email_verified'] !== 1) {
        sendJson(403, [
            'success' => false,
            'message' => 'Please verify your email first. Check your Etown inbox for the link.'
        ]);
    }
    if (!$record || !password_verify($userpassword, $record['password'])) {
        sendJson(401, [
            'success' => false,
            'message' => 'Invalid email or password.'
        ]);
    }

    $token = createSessionToken((int)$record['userid']);

    $user_data = getUserById((int)$record['userid']);

    sendJson(200, [
        'success' => true,
        'message' => 'Login successful.',
        'token' => $token,
        'user' => $user_data,
    ]);
}

/*
------------------------------------------------------------
POST /api/users/logout
------------------------------------------------------------
*/
if ($routePart === 'logout') {
    if ($method !== 'POST') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }

    $token = getBearerToken();
    if ($token !== null) {
        revokeSessionToken($token);
    }

    sendJson(200, ['success' => true, 'message' => 'Logout successful.']);
}
//verify
if ($routePart === 'verify') {
    if ($method !== 'POST') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }

    $data = getJsonBody();
    $token = trim((string)($data['token'] ?? ''));

    // Real tokens are exactly 64 hex characters
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        sendJson(400, ['success' => false, 'message' => 'Invalid verification link.']);
    }

    if (verifyEmailToken($token)) {
        sendJson(200, ['success' => true, 'message' => 'Your email is verified! You can log in now.']);
    }

    sendJson(400, ['success' => false, 'message' => 'This link is invalid or has expired.']);
}

/*
------------------------------------------------------------
GET /api/users/profile
PUT /api/users/profile
------------------------------------------------------------
*/
if ($routePart === 'profile') {
    $userId = requireAuth();

    if ($method === 'GET') {
        sendJson(200, ['success' => true, 'data' => getUserById($userId)]);
    }

    if ($method === 'PUT') {
        $user = updateUserProfile($userId, getJsonBody(), false);
        sendJson(200, [
            'success' => true,
            'message' => 'Profile updated.',
            'data' => $user,
        ]);
    }

    sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
}

/*
------------------------------------------------------------
GET /api/users/search-options
Lists for the search dropdowns
------------------------------------------------------------
*/
if ($routePart === 'search-options') {
    if ($method !== 'GET') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }
    requireAuth();

    sendJson(200, [
        'success'   => true,
        'countries' => WFDatabase::getDataFromSQL(
            "SELECT COUNTRY_ID, COUNTRY_NAME FROM wf_countries ORDER BY COUNTRY_NAME", []),
        'regions'   => WFDatabase::getDataFromSQL(
            "SELECT REGION_ID, REGION_NAME FROM wf_world_regions ORDER BY REGION_NAME", []),
        'languages' => WFDatabase::getDataFromSQL(
            "SELECT LANGUAGE_ID, LANGUAGE_NAME FROM wf_languages ORDER BY LANGUAGE_NAME", []),
    ]);
}

/*
------------------------------------------------------------
GET /api/users/search?country=ID&region=ID&language=ID
Any combination of filters, at least one required
------------------------------------------------------------
*/
if ($routePart === 'search') {
    if ($method !== 'GET') {
        sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
    }
    requireAuth();

    $countryId  = (int)($_GET['country'] ?? 0);
    $regionId   = (int)($_GET['region'] ?? 0);
    $languageId = (int)($_GET['language'] ?? 0);

    if ($countryId <= 0 && $regionId <= 0 && $languageId <= 0) {
        sendJson(400, ['success' => false, 'message' => 'Pick at least one filter.']);
    }

    // Only public info: no emails or password hashes
    $sql = "
        SELECT u.userid, u.first_name, u.last_name,
               c.COUNTRY_NAME, r.REGION_NAME,
               (SELECT GROUP_CONCAT(l.LANGUAGE_NAME ORDER BY l.LANGUAGE_NAME SEPARATOR ', ')
                  FROM user_spoken_languages usl2
                  JOIN wf_languages l ON l.LANGUAGE_ID = usl2.language_id
                 WHERE usl2.userid = u.userid) AS languages
        FROM users u
        LEFT JOIN wf_countries c ON c.COUNTRY_ID = u.original_country_id
        LEFT JOIN wf_world_regions r ON r.REGION_ID = c.REGION_ID
        WHERE 1 = 1";
    $params = [];

    if ($countryId > 0) {
        $sql .= " AND u.original_country_id = :country";
        $params[':country'] = $countryId;
    }
    if ($regionId > 0) {
        $sql .= " AND c.REGION_ID = :region";
        $params[':region'] = $regionId;
    }
    if ($languageId > 0) {
        $sql .= " AND EXISTS (SELECT 1 FROM user_spoken_languages usl
                              WHERE usl.userid = u.userid AND usl.language_id = :language)";
        $params[':language'] = $languageId;
    }

    $sql .= " ORDER BY u.last_name, u.first_name LIMIT 100";

    sendJson(200, ['success' => true, 'data' => WFDatabase::getDataFromSQL($sql, $params)]);
}

/*
TODO: Complete these endpoints
------------------------------------------------------------
Admin collection: GET /api/users
------------------------------------------------------------
------------------------------------------------------------
Admin item: GET, PUT, DELETE /api/users/{userid}
------------------------------------------------------------
*/

/*
------------------------------------------------------------
GET/POST /api/users/{userid}/languages
DELETE   /api/users/{userid}/languages/{languageid}
------------------------------------------------------------
*/

// Only urls shaped like /api/users/{number}/languages enter this block
if ($routePart !== null && ctype_digit($routePart) && $subResource === 'languages') {
    //($userIndex+1 = userId, +2 = "languages", +3 = languageId)
    $userId = (int)$routePart;
    $languageId = $parts[$usersIndex + 3] ?? null; // for DELETE /{languageid}

    // Only the logged-in owner can add or remove their own languages
    if ($method !== 'GET') {
        $authUserId = requireAuth();
        if ($authUserId !== $userId) {
            sendJson(403, ['success' => false, 'message' => 'You can only edit your own languages.']);
        }
    }

    // Gets a list of every language that this user speaks
    if ($method === 'GET') {
        $sql = "
            SELECT l.LANGUAGE_ID, l.LANGUAGE_NAME, usl.proficency_level
            FROM wf_languages l
            JOIN user_spoken_languages usl ON usl.language_id = l.LANGUAGE_ID
            WHERE usl.userid = :userid
            ORDER BY l.LANGUAGE_NAME
        ";
        $data = WFDatabase::getDataFromSQL($sql, [':userid' => $userId]);
        sendJson(200, ['success' => true, 'data' => $data]);
    }

    // Links a new language to a user
    if ($method === 'POST') {
        $body = getJsonBody();
        $languageIdToAdd = (int)($body['language_id'] ?? 0);
        $proficiency = (int)($body['proficency_level'] ?? 0);

        $minLevel = 1;
        $maxLevel = 5;

        if ($languageIdToAdd <= 0) {
            sendJson(400, ['success' => false, 'message' => 'A valid language_id is required.']);
        }

        if ($proficiency < $minLevel || $proficiency > $maxLevel) {
            sendJson(400, ['success' => false, 'message' => "proficency_level must be between $minLevel and $maxLevel."]);
        }

        $sql = "INSERT INTO user_spoken_languages (userid, language_id, proficency_level) VALUES (:userid, :language_id, :proficency_level)";
        try {
            WFDatabase::executeSQL($sql, [':userid' => $userId, ':language_id' => $languageIdToAdd, ':proficency_level' => $proficiency]);
            sendJson(201, ['success' => true, 'message' => 'Language added.']);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                sendJson(409, ['success' => false, 'message' => 'Invalid language_id or already linked to this user.']);
            }
            sendJson(500, ['success' => false, 'message' => 'Database error.']);
        }
    }

    if ($method === 'DELETE' && $languageId !== null && ctype_digit($languageId)) {
        $sql = "DELETE FROM user_spoken_languages WHERE userid = :userid AND language_id = :language_id";
        WFDatabase::executeSQL($sql, [':userid' => $userId, ':language_id' => (int)$languageId]);
        sendJson(200, ['success' => true, 'message' => 'Language removed.']);
    }

    sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
}

sendJson(404, ['success' => false, 'message' => 'Endpoint not found.']);