<h2>Login</h2>
    <div class="w3-section" id="login-form">
        <form action="processes/login.php" method="POST">
            <!-- name stays 'user' so processes/login.php still works; it's the email -->
            <label>Etown Email:
                <input type="email" name="user" placeholder="you@etown.edu" autocomplete="username" required>
            </label><BR><BR>
            <label>Password:
                <input type="password" name="pass" autocomplete="current-password" required>
            </label><BR><BR>
            <input type="submit" value="Login"><BR>
        </form>
        <a href="login.php?page=register">Register</a>
    </div>