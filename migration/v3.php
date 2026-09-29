<?PHP


// double check whether these tables are the same as v2, and if so
// drop user_countries from migration and point my WFDatabase queries
// at user_country_interests instead

$sql = "
    CREATE TABLE IF NOT EXISTS user_regions (
        userid INT NOT NULL,
        REGION_ID INT NOT NULL,
        date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (userid, REGION_ID),
        FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE,
        FOREIGN KEY (REGION_ID) REFERENCES wf_world_regions(REGION_ID)
    );
";

// had this originally:
/*
    CREATE TABLE IF NOT EXISTS user_countries (
        userid INT NOT NULL,
        COUNTRY_ID INT NOT NULL,
        date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (userid, COUNTRY_ID),
        FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE,
        FOREIGN KEY (COUNTRY_ID) REFERENCES wf_countries(COUNTRY_ID)
    );

*/


?>

