<?PHP

function publicUserSelect(): string
{
    return "
        SELECT
            u.userid,
            u.email,
            u.first_name,
            u.last_name,
            u.date_registered,
            u.original_country_id,
            c.COUNTRY_NAME AS original_country_name,
            u.role
        FROM users u
        LEFT JOIN wf_countries c
            ON c.COUNTRY_ID = u.original_country_id
    ";
}

function getUserById( int $userId): ?array
{
    $sql = publicUserSelect() . ' WHERE u.userid = :userid';
    $params = [':userid' => $userId];
    $users = WFDatabase::getDataFromSQL($sql,$params);
    if(is_array($users)){
        $user = $user[0];
    }
    return $user ?: null;
}

function validateCountryId(mixed $countryId): ?int
{
    if ($countryId === null || $countryId === '') {
        return null;
    }

    $countryIdString = (string)$countryId;

    if (!ctype_digit($countryIdString)) {
        sendJson(400, [
            'success' => false,
            'message' => 'original_country_id must be numeric or null.'
        ]);
    }

    $id = (int)$countryIdString;
    $sql = 'SELECT COUNTRY_ID FROM wf_countries WHERE COUNTRY_ID = :country_id';
    $params = [':country_id' => $id];
    $countries = WFDatabase::getDataFromSQL($sql,$params);

    if (!is_array($countries) || count($countries)<=0) {
        sendJson(400, [
            'success' => false,
            'message' => 'original_country_id does not identify a valid country.'
        ]);
    }
    $id = $countries[0]["COUNTRY_ID"];

    return $id;
}

function updateUserProfile(int $userId, array $data, bool $adminMode = false): array
{
    $allowed = ['email', 'first_name', 'last_name', 'original_country_id'];

    if ($adminMode) {
        $allowed[] = 'role';
    }

    $updates = [];
    $params = [':userid' => $userId];

    foreach ($allowed as $field) {
        if (!array_key_exists($field, $data)) {
            continue;
        }

        if ($field === 'email') {
            $email = trim((string)$data[$field]);
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                sendJson(400, ['success' => false, 'message' => 'A valid email address is required.']);
            }

            $check_sql = 'SELECT userid FROM users WHERE email = :email AND userid <> :userid';
            $check_params = [':email' => $email, ':userid' => $userId];
            $users = WFDatabase::getDataFromSQL($check_sql,$check_params);
            if (is_array($users) && count($users)>0 ) {
                sendJson(409, ['success' => false, 'message' => 'That email address is already registered.']);
            }

            $updates[] = 'email = :email';
            $params[':email'] = $email;
            continue;
        }

        if ($field === 'original_country_id') {
            $countryId = validateCountryId($pdo, $data[$field]);
            $updates[] = 'original_country_id = :original_country_id';
            $params[':original_country_id'] = $countryId;
            continue;
        }

        if ($field === 'role') {
            if (!in_array($data[$field], ['user', 'admin'], true)) {
                sendJson(400, ['success' => false, 'message' => 'role must be user or admin.']);
            }

            $updates[] = 'role = :role';
            $params[':role'] = $data[$field];
            continue;
        }

        $value = trim((string)$data[$field]);
        $updates[] = $field . ' = :' . $field;
        $params[':' . $field] = $value === '' ? null : $value;
    }

    if (array_key_exists('password', $data)) {
        $password = (string)$data['password'];

        if (strlen($password) < 8) {
            sendJson(400, [
                'success' => false,
                'message' => 'Password must be at least 8 characters long.'
            ]);
        }

        $updates[] = 'password = :password';
        $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (count($updates) === 0) {
        sendJson(400, [
            'success' => false,
            'message' => 'No supported profile fields were supplied.'
        ]);
    }

    $update_sql = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE userid = :userid';
    WFDatabase::executeSQL($update_sql,$params);

    $user = getUserById( $userId);

    if ($user === null) {
        sendJson(404, ['success' => false, 'message' => 'User not found.']);
    }

    return $user;
}
?>