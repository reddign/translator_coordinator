<?PHP

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

//I will login the user and send them to the home page.
$fname = $_POST["first"];
$lname = $_POST["last"];
$u = $_POST["user"];
$e = $_POST["email"];
$p = $_POST["pass"];

echo "Need to send registration data to API<BR><BR>";
print_r($_POST);


//header("Location: ../index.php");
//exit;

?>