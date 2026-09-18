<?PHP
 
 /*
 ------------------------------------------------------------
 Secure Logout
 ------------------------------------------------------------
 */
 ini_set('display_errors', 1);
 error_reporting(E_ALL & ~E_NOTICE);

 session_start();


 // Remove authentication data
 unset($_SESSION["user"]);
 unset($_SESSION["LoginStatus"]);
 if( isset($_SESSION["api_token"]) ) {
    unset($_SESSION["api_token"]);
 }

 // Clear session variables
 $_SESSION = [];


// Future session logout tracking will go here
# TODO: Record logout timestamp.
# TODO: Associate logout time with authenticated user ID.
# TODO: Associate logout time with session ID.
# TODO: Invalidate authentication token.


 /*
 ---------------------------------------------------------
 Clear session data.
 ---------------------------------------------------------
 */

 session_destroy();

 session_start();
 session_regenerate_id(true);

 /*
 ---------------------------------------------------------
 Return user to login page.
 ---------------------------------------------------------
 */
 header("location:../login.php?page=login");
 exit;
?>