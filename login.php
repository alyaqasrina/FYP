<?php
session_start();
include('db.php'); // Ensure database connection is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check email and password
    $query = "SELECT user_id, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Verify password
    if ($user_id && password_verify($password, $hashed_password)) {
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email; // Optional, if you need email in the session
        header("Location: homepage.php");
        exit();
    } else {
        echo "<p>Invalid email or password.</p>";
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