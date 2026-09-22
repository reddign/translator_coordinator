<?PHP

// Scott D add group_description to groups table

$sql = "ALTER TABLE groups 
        ADD group_description varchar(255);";
?>