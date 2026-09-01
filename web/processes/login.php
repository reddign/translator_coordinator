<?PHP

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
define("PROJECT_ROOT", dirname(__DIR__, 1));


//I will login the user and send them to the home page.
$u = $_POST["user"];
$p = $_POST["pass"];
$result = [];

if(is_array($result) && count($result) > 0 ){
    echo "logged in";
    $_SESSION["LoginStatus"]="YES";
    $_SESSION["userID"]=$result[0]["userID"];
    $_SESSION["Name"] = $result[0]["first"];
    header("location:../login.php?page=profile");
}else{
    echo "not correct password";
    $_SESSION["LoginStatus"]="NO";
    $_SESSION["userID"]="";
    $_SESSION["Name"] = "";
    header("location:../login.php?page=login");
}

print_r($_SESSION);


?>