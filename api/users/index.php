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

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(400, ['success' => false, 'message' => 'A valid email address is required.']);
    }

    if (strlen($userpassword) < 8) {
        sendJson(400, ['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    }

    $check_sql = 'SELECT userid FROM users WHERE email = :email';
    $check_params = [':email' => $email];
      
    $user_already_exist_results = WFDatabase::getDataFromSQL($check_sql,$check_params);

    if (count($user_already_exist_results)>0) {
        sendJson(409, ['success' => false, 'message' => 'That email address is already registered.']);
    }
    #TODO: Should validate the country id
    $countryId = $data['original_country_id']; 

    $insert_user_sql = "INSERT INTO users
    (email, first_name, last_name, date_registered, original_country_id, password, role)
    VALUES
    (:email, :first_name, :last_name, :date_registered, :original_country_id, :password, 'user')";
    
    $insert_params = [
        ':email' => $email,
        ':first_name' => isset($data['first_name']) && trim((string)$data['first_name']) !== '' ? trim((string)$data['first_name']) : null,
        ':last_name' => isset($data['last_name']) && trim((string)$data['last_name']) !== '' ? trim((string)$data['last_name']) : null,
        ':date_registered' => date('Y-m-d H:i:s'),
        ':original_country_id' => $countryId,
        ':password' => password_hash($userpassword, PASSWORD_DEFAULT),
    ];
    
    $userId = WFDatabase::executeSQL($insert_user_sql,$insert_params);
    #TODO: Manage session tokens in the DB eventually

    $user_data = getUserById($userId);


    sendJson(201, [
        'success' => true,
        'message' => 'Registration successful.',
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

    $user_login_sql = 'SELECT userid, password FROM users WHERE email = :email LIMIT 1';
    $login_params = [':email' => $email];
    $records = WFDatabase::getDatafromSQL($user_login_sql,$login_params);
    $record = $records[0];
    if (!$record || !password_verify($userpassword, $record['password'])) {
        sendJson(401, [
            'success' => false,
            'message' => 'Invalid email or password.'
        ]);
    }

    //TODO: Manage sessions in database
    //$token 
    
    $user_data = getUserById((int)$record['userid']);
    
    sendJson(200, [
        'success' => true,
        'message' => 'Login successful.',
        #'token' => $token,
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
    //TODO: Manage revoking sessions in db
    session_destroy();
    sendJson(200, ['success' => true, 'message' => 'Logout successful.']);
}

/*
------------------------------------------------------------
GET /api/users/profile
PUT /api/users/profile
------------------------------------------------------------
*/
if ($routePart === 'profile') {
    //TODO: Need to validate session and userid from DB
    //$authenticated 
    //$userId = (int)$authenticated['userid'];

    if ($method === 'GET') {
        sendJson(200, ['success' => true, 'data' => getUserById($pdo, $userId)]);
    }

    if ($method === 'PUT') {
        $user = updateUserProfile($pdo, $userId, getJsonBody(), false);
        sendJson(200, [
            'success' => true,
            'message' => 'Profile updated.',
            'data' => $user,
        ]);
    }

    sendJson(405, ['success' => false, 'message' => 'Method not allowed.']);
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


    //Gets a list of every langiage that this user speaks, with proficiency
    if ($method === 'GET') {
        
        //Joins the user+spoken_language table on wf_languages by language_id, but displays by language name
        $sql = "
            SELECT l.LANGUAGE_ID, l.LANGUAGE_NAME
            FROM wf_languages l
            JOIN user_spoken_languages usl ON usl.language_id = l.LANGUAGE_ID
            WHERE usl.userid = :userid
            ORDER BY l.LANGUAGE_NAME
        ";
        $data = WFDatabase::getDataFromSQL($sql, [':userid' => $userId]);
        sendJson(200, ['success' => true, 'data' => $data]);
    }


    //Links a new language to a user by inserting a new row into user_spoken_language table
    if ($method === 'POST') {
        $body = getJsonBody();
        $languageIdToAdd = (int)($body['language_id'] ?? 0);
        $proficiency = (int)($body['proficency_level'] ?? 0);

        $minLevel = 1;
        $maxLevel = 5;

        //Rejects any invalid language ids before hitting the DB
        if ($languageIdToAdd <= 0) {
        sendJson(400, ['success' => false, 'message' => 'A valid language_id is required.']);
        }

        //Rejects any proficiency level outside of 1-5
        if ($proficiency < $minLevel || $proficiency > $maxLevel) {
        sendJson(400, ['success' => false, 'message' => "proficency_level must be between $minLevel and $maxLevel."]);
        }

        $sql = "INSERT INTO user_spoken_languages (userid, language_id, proficency_level) VALUES (:userid, :language_id, :proficency_level)";
        try {
            WFDatabase::executeSQL($sql, [':userid' => $userId, ':language_id' => $languageIdToAdd, ':proficency_level' => $proficiency]);
            sendJson(201, ['success' => true, 'message' => 'Language added.']);
        }   catch (PDOException $e) {
    
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


sendJson(404, ['success' => false, 'message' => 'Endpoint not found.']);



