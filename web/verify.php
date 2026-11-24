<?php
require_once __DIR__ . "/../includes/config.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
    }
}

$token = trim($_GET['token'] ?? '');
$success = false;

if ($token === '') {
    $message = "No verification token was provided.";
} else {
    $result = apiRequest("POST", $mainURL . "/api/users/verify", ["token" => $token]);

    if ($result === null) {
        $message = "Verification isn't working right now. Try again later.";
    } else {
        $success = !empty($result['success']);
        $message = $result['message'] ?? "Something went wrong.";
    }
}
?>

<h2>Email Verification</h2>

<div class="w3-section">
    <?php if ($success): ?>
        <p><?php echo e($message); ?></p>
        <p><a href="login.php?page=login">Go to login</a></p>
    <?php else: ?>
        <div class="gerror"><?php echo e($message); ?></div>
        <p><a href="login.php?page=register">Back to registration</a></p>
    <?php endif; ?>
</div>

<BR><BR><BR><BR>

<?php include "includes/footer.php"; ?>