<?PHP

$latest_version = 1;

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

//check the current version
$sql_tables = "SHOW TABLES;";
$tables = WFDatabase::getDataFromSQL($sql_tables);
$version_table_found = false;
foreach($tables as $table){
    if($table["Tables_in_translator_coordinator_db"]=="db_version"){
        $version_table_found =true;
    }
}

if($version_table_found){
    $sql = "SELECT version from db_version;";
    $version = WFDatabase::getDataFromSQL($sql);
}else{
    $version = false;
}

if($version && count($version)>0){
    $start_version = $version[0]["version"];

    if($start_version==$latest_version){
        echo "Database is up to date with the latest version #".$latest_version.".";
        echo "<BR>No migration needed.";
        exit;
    }
}else{
    $start_version = 0;
}


//Loop from start_version to latest_version
for($i=$start_version+1;$i<=$latest_version;$i++){
    $fileName = "v".$i.".php";
    //if file exists, then require it.
    require $fileName;
    //execute the SQL stored in the $sql variable from the file.
    WFDatabase::executeSQL($sql);
    
    
}

$sql = "UPDATE db_version set version = :v;";
$params = [":v"=>$latest_version];
WFDatabase::executeSQL($sql,$params);

echo "Database was upgraded from version ".$start_version." to version ".$latest_version.".";

?>