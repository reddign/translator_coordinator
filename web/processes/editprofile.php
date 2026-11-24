<?PHP

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once __DIR__ . "/../../includes/config.php";
/*
------------------------------------------------------------
Make sure the page was accessed using POST.
------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php?page=register");
    exit;
}
/*
------------------------------------------------------------
Read form values.
------------------------------------------------------------
*/
$languageId = $_POST["language_id"] ?? "";