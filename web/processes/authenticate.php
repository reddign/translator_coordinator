<?PHP
   ini_set('display_errors', 1);
   error_reporting(E_ALL & ~E_NOTICE);
   session_start();
   //if not logged in kill their session and send back to login page
   if(!isset($_SESSION["LoginStatus"]) || $_SESSION["LoginStatus"]!="YES"){
      header("Location: login.php?page=login");
      exit;
   }

?>