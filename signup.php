<?php
require('db.php');

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm-password']) && isset($_POST['phone_number'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($conn, $username);
    $phoneNumber = stripslashes($_REQUEST['phone_number']);
    $phoneNumber = mysqli_real_escape_string($conn, $phoneNumber);
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($conn, $email);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($conn, $password);
    $confirm_password = stripslashes($_REQUEST['confirm-password']);
    $confirm_password = mysqli_real_escape_string($conn, $confirm_password);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<div class='form'>
              <h3>Passwords do not match.</h3><br/>
              <p class='link'>Click here to <a href='signup.php'>register</a> again.</p>
              </div>";
    } else {
        // Check if username or email already exists
        $query = "SELECT * FROM `users` WHERE username='$username' OR email='$email'";
        $result = mysqli_query($conn, $query) or die(mysqli_error($con));
        $rows = mysqli_num_rows($result);

        if ($rows > 0) {
            echo "<div class='form'>
                  <h3>Username or email already exists.</h3><br/>
                  <p class='link'>Click here to <a href='signup.php'>register</a> again.</p>
                  </div>";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO `users` (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                echo "<div class='form'>
                      <h3>You have registered successfully.</h3><br/>
                      <p class='link'>Click here to <a href='login.php'>login</a></p>
                      </div>";
            } else {
                echo "<div class='form'>
                      <h3>Required fields are missing.</h3><br/>
                      <p class='link'>Click here to <a href='signup.php'>register</a> again.</p>
                      </div>";
            }
        }
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

    <section class="signup">
    <div class="signup-form">
        <h2 class="signup-title">Sign Up</h2>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
            <p class="login-page-signup">Already have an account? <a href="login.php">Login here</a></p>
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