<?PHP

$sql = "
    CREATE TABLE IF NOT EXISTS user_country_interests (
        id INT(11) NOT NULL AUTO_INCREMENT,
        userid INT(11) NOT NULL,
        country_id INT(11) NOT NULL,
        createdOn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id),

        CONSTRAINT fk_uci_user
            FOREIGN KEY (userid)
            REFERENCES users(userid)
            ON DELETE CASCADE,

        CONSTRAINT fk_uci_country
            FOREIGN KEY (country_id)
            REFERENCES wf_countries(COUNTRY_ID)
            ON DELETE CASCADE,

        UNIQUE KEY unique_user_country (userid, country_id)
    );

    ALTER TABLE users
    ADD COLUMN bio TEXT NULL;
";

?>
