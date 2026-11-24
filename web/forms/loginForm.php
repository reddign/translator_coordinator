
<?php if ($page === "forgot-password") { ?>

    <h2>Forgot Password</h2>

    <div class="w3-section" id="forgot-password-form">

        <form action="processes/login.php" method="POST">

            <input type="hidden" name="action" value="forgot-password">

            <label>Etown Email:
                <input type="email" name="email"
                       placeholder="you@etown.edu" required>
            </label>
            <BR><BR>

            <label>New Password:
                <input type="password" name="new_password"
                       minlength="8" required>
            </label>
            <BR><BR>

            <label>Retype New Password:
                <input type="password" name="confirm_password"
                       minlength="8" required>
            </label>
            <BR><BR>

            <input type="submit" value="Reset Password">

        </form>

        <a href="login.php?page=login">Back to Login</a>

    </div>

<?php } else { ?>

    <h2><?php echo t('login'); ?></h2>

    <div class="w3-section" id="login-form">

        <form action="processes/login.php" method="POST">

            <label>Etown Email:
                <input type="email" name="user" placeholder="you@etown.edu" autocomplete="username" required>
            </label>
            <BR><BR>

            <label><?php echo t('password'); ?>:
                <input type="password" name="pass" autocomplete="current-password" required>
            </label>
            <BR><BR>

            <input type="submit" value="Login"><BR>

        </form>

        <a href="login.php?page=register">Register</a>
        &nbsp; | &nbsp;
        <a href="login.php?page=forgot-password">Forgot Password?</a>

    </div>

<?php } ?>
