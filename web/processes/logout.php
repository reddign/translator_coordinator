<?PHP
 
 /*
 ------------------------------------------------------------
 Secure Logout
 ------------------------------------------------------------
 */

 # TODO: Create logout.php endpoint.
 # TODO: Remove authentication token from session.
 # TODO: Clear all session variables.
 # TODO: Destroy the user's session.
 # TODO: Redirect user to login page after logout.
 #TODO: record logout times


 ini_set('display_errors', 1);
 error_reporting(E_ALL & ~E_NOTICE);

 session_start();

 /*
 ---------------------------------------------------------
 Logout activity tracking. (Future addition)
 ---------------------------------------------------------
 */
 # TODO: Save logout time to user_sessions table.
 # TODO: Update session record using session_id().
 # TODO: Associate logout time with authenticated user.


 /*
 ---------------------------------------------------------
 Clear session data.
 ---------------------------------------------------------
 */

 $_SESSION = [];

 session_destroy();

 /*
 ---------------------------------------------------------
 Return user to login page.
 ---------------------------------------------------------
 */
 header("location:../login.php?page=login");
 exit;
?>