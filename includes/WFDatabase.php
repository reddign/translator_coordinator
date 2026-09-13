<?PHP

class WFDatabase{
    private static $conn = null;

    private static function connect() {
        global $servername, $database, $username, $password,$port;
        if($port === null){
            $port = 3306;
        }
           
        if (self::$conn === null) {
            try {
                self::$conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                http_response_code(500);
                exit();
            }
        }
    }

    public static function getDataFromSQL($sql,$params=null){
        self::connect();
        
        $stmt = self::$conn->prepare($sql);
        $stmt->execute($params);
        //$stmt->execute();
        
        // set the resulting array to associative
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $valuesArray = $stmt->fetchAll();
        return $valuesArray;
    }

    public static function executeSQL($sql,$params=null,$returnID=false){
        self::connect();
        
        $stmt = self::$conn->prepare($sql);
        $stmt->execute($params);

        if ($returnID) {
            return self::$conn->lastInsertId();
        } else {
            return true;
        }
    }

    public static function startTransaction() {
        self::connect();
        self::$conn->beginTransaction();
    }

    public static function commitTransaction() {
        self::$conn->commit();
    }

    public static function rollbackTransaction() {
        self::$conn->rollback();
    }
}

/*
ADDITIONAL FUNCTIONS:

SCRUM STORY: view another user's public profile

- field names match what profile.php already pulls from $_SESSION["user"]:
first_name, last_name, original_country_name, email, role, date_registered...
so this can plug into the same users query that is already done
*/

public function getPublicProfile($userId) {
    // TODO: query something like:
    // SELECT first_name, last_name, original_country_name
    // FROM users WHERE id = :userId
    echo "This is where values will be pulled from the database (getPublicProfile).";
    return [
        "first_name"            => null,
        "last_name"             => null,
        "original_country_name" => null,
    ];
}

public function getAssociatedCountries($userId) {
    // TODO: real query against a user_countries table
    echo "This is where associated countries will be pulled from the database.";
    return [];
}

public function getUserLanguages($userId) {
    // TODO: real query
    echo "This is where the user's spoken languages will be pulled from the database.";
    return [];
}

public function getUserInterests($userId) {
    // TODO: real query
    echo "This is where the user's interests will be pulled from the database.";
    return [];
}

?>