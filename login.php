<?php
session_start(); // Start the session
require('db.php');

if (isset($_POST['submit'])) {
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($conn, $email);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($conn, $password);

    // Search for the email in the database
    $email_search = "SELECT * FROM users WHERE email='$email'";
    $query = mysqli_query($conn, $email_search);

    $email_count = mysqli_num_rows($query);

    if ($email_count) {
        $email_pass = mysqli_fetch_assoc($query);
        $db_pass = $email_pass['password'];

        // Verify the password
        $pass_decode = password_verify($password, $db_pass);

        if ($pass_decode) {
            // Set the session variable for username
            $_SESSION['username'] = $email_pass['username'];
            // Redirect to homepage
            header("Location: homepage.php");
            exit(); // Always use exit after header redirection
        } else {
            echo "Password incorrect";
        }
    } else {
        echo "Invalid email";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css" />
    <title>Calendify</title> 
    <nav class="navbar">
        <div class="logo">
            <img src="logo_new.png" alt="Calendify Logo" href="index.php">
            <h3 class="navbar-title">CALENDIFY</h3>
        </div>
    </nav>
</head>
<body>
    <section class="login">
        <div class="login-form">
            <form action="login.php" method="POST">
                <h2 class="login-title">Log in to your Calendify account</h2>
                <div class="form-group">
                    <label for="email" class="email-label">Email address</label>
                    <input type="email" id="email" name="email" class="input-field" placeholder="example@gmail.com" required><br>
                </div>
                <div class="form-group">
                    <label for="password" class="password-label">Password</label>
                    <input type="password" id="password" name="password" class="input-field" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Log in</button>
                <p class="login-signup">Don't have an account? <a href="signup.php">Sign Up here</a></p>
            </form>
        </div>
    </section>

    <section class="footer-section">
        <footer class="footer">
            <div class="footer-container">
                <a href="homepage.php">Home</a>
                <a href="aboutus.php">About Us</a>
                <a href="privacypolicy.php">Privacy Policy</a>
            </div>
            <div class="footer-bottom">
                &copy; 2024 Calendify. All rights reserved.
            </div>
        </footer>
    </section>
</body>
</html>