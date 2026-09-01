<?PHP

$sql = "
    CREATE TABLE IF NOT EXISTS db_version(
        version INT default 0
    );

    INSERT INTO db_version (version) VALUES (0);


    ALTER TABLE users
    ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER password,
    ADD UNIQUE INDEX uq_users_email (email),
    DROP COLUMN userscol;

    ALTER TABLE users
    CHANGE COLUMN date_registered date_registered DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

    CREATE TABLE IF NOT EXISTS api_sessions (
        session_id BIGINT NOT NULL AUTO_INCREMENT,
        userid INT NOT NULL,
        token_hash CHAR(64) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        expires_at DATETIME NOT NULL,
        PRIMARY KEY (session_id),
        UNIQUE INDEX uq_api_sessions_token_hash (token_hash),
        INDEX idx_api_sessions_userid (userid),
        INDEX idx_api_sessions_expires_at (expires_at),
        CONSTRAINT fk_api_sessions_user
            FOREIGN KEY (userid)
            REFERENCES users (userid)
            ON DELETE CASCADE
            ON UPDATE NO ACTION
    ) ENGINE = InnoDB;

";

?>