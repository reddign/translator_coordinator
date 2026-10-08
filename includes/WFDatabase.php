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


    /*
    US-05: associate additional countries with a user
    US-06: associate regions with a user

    tables come from the migration file (user_countries, user_regions)

    - field names match what profile.php already pulls from $_SESSION["user"]:
    first_name, last_name, original_country_name, email, role, date_registered...
    so this can plug into the same users query that is already done
    */

    // below are the get, add, and remove functions for both countries and regions

    public static function getUserCountries($userId) {
        $sql = "
            SELECT c.COUNTRY_ID, c.COUNTRY_NAME
            FROM user_country_interests uc
            JOIN wf_countries c ON c.COUNTRY_ID = uc.COUNTRY_ID
            WHERE uc.userid = :userid
            ORDER BY c.COUNTRY_NAME
            ";
            return self::getDataFromSQL($sql, [":userid" => $userId]);
    }

    public static function addUserCountry($userId, $countryId) {
        $existing = self::getDataFromSQL(
            "SELECT 1 FROM user_country_interests WHERE userid = :userid AND country_id = :country_id",
            [":userid" => $userId, ":country_id" => $countryId]
        );
 
        if (!empty($existing)) {
            return ["success" => false, "message" => "That country is already on your profile."];
        }
 
        try {
            self::executeSQL(
                "INSERT INTO user_country_interests (userid, country_id) VALUES (:userid, :country_id)",
                [":userid" => $userId, ":country_id" => $countryId]
            );
        } catch (PDOException $e) {
            // e.g. country id doesn't exist (foreign key) or a duplicate slipped through
            return ["success" => false, "message" => "Could not add that country."];
        }
 
        return ["success" => true, "message" => "Country added."];
    }

    public static function removeUserCountry($userId, $countryId) {
        self::executeSQL(
            "DELETE FROM user_country_interests WHERE userid = :userid AND country_id = :country_id",
            [":userid" => $userId, ":country_id" => $countryId]
        );

        return ["success" => true, "message" => "Country removed."];
    }

    public static function getUserRegions($userId) {
        $sql = "
            SELECT r.REGION_ID, r.REGION_NAME
            FROM user_regions ur
            JOIN wf_world_regions r ON r.REGION_ID = ur.REGION_ID
            WHERE ur.userid = :userid
            ORDER BY r.REGION_NAME
            ";
        return self::getDataFromSQL($sql, [":userid" => $userId]);
    }

    public static function addUserRegion($userId, $regionId) {
        $existing = self::getDataFromSQL(
            "SELECT 1 FROM user_regions WHERE userid = :userid AND REGION_ID = :region_id",
            [":userid" => $userId, ":region_id" => $regionId]
        );
 
        if (!empty($existing)) {
            return ["success" => false, "message" => "That region is already on your profile."];
        }
 
        try {
            self::executeSQL(
                "INSERT INTO user_regions (userid, REGION_ID) VALUES (:userid, :region_id)",
                [":userid" => $userId, ":region_id" => $regionId]
            );
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Could not add that region."];
        }
 
        return ["success" => true, "message" => "Region added."];
    }

    public static function removeUserRegion($userId, $regionId) {
        self::executeSQL(
            "DELETE FROM user_regions WHERE userid = :userid AND REGION_ID = :region_id",
            [":userid" => $userId, ":region_id" => $regionId]
        );
 
        return ["success" => true, "message" => "Region removed."];
    }

    /*
        US-07: view another user's public profile
        - view_profile.php calls them as method
    */
    public function getPublicProfile($userId) {
        // SELECT first_name, last_name, original_country_name
        // FROM users WHERE id = :userId
        echo "This is where values will be pulled from the database (getPublicProfile).";
        $sql = "
            SELECT
                u.userid,
                u.first_name,
                u.last_name,
                u.original_country_id,
                c.COUNTRY_NAME AS original_country_name
            FROM users u
            LEFT JOIN wf_countries c ON c.COUNTRY_ID = u.original_country_id
            WHERE u.userid = :userid
            ";
        $params = [":userid" => $userId];
        $result = self::getDataFromSQL($sql, $params);
        return $result[0] ?? null;
    }

    public function getAssociatedCountries($userId) {
        // reuses the US-05 lookup method
        return [self::getUserCountries($userId)];
    }

    public function getUserLanguages($userId) {
        // TODO: real query
        return [];
    }

}

?>