<?PHP

$sql = "
    ALTER TABLE user_spoken_languages CHANGE user_spoken_languagescol isTranslator TINYINT DEFAULT 0;
";

?>