<?php
require('db.php');

if (isset($_POST['submit'])) {
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($con, $email);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con, $password);

    $email_search = "SELECT * FROM users WHERE email='$email'";
    $query = mysqli_query($con, $email_search);

    $email_count = mysqli_num_rows($query);

    if ($email_count) {
        $email_pass = mysqli_fetch_assoc($query);
        $db_pass = $email_pass['password'];

        $_SESSION['username'] = $email_pass['username'];

        $pass_decode = password_verify($password, $db_pass);

        if ($pass_decode) {
            echo "Login successful";
            ?>
            <script>
                location.replace("index.php");
            </script>
            <?php
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Calendify</title>
</head>
<body>
    <section class="login-container">
        <div class="login-form-wrapper">
            <div class="login-content">
                <form class="login-form" action="login.php" method="POST">
                    <img src="calendify/logo.png" alt="Calendify logo" class="logo">
                    <h2 class="login-title">Log in to your Calendify account</h2>
                    <label for="email" class="input-label">Email address</label><br>
                    <input type="email" id="email" name="email" class="input-field" placeholder="example@gmail.com" required><br>
                    <label for="password" class="password-label">Password</label>
                    <input type="password" id="password" name="password" class="input-field" placeholder="Enter your password" required>
                    <div class="footer-content-login">
                        <button type="submit" name="submit" class="submit-button">Log in</button>
                        <div class="register-link-wrapper">
                            <span>New User?</span>
                            <a href="register.php" style="text-decoration: underline; color: #249add">Register Here</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
</html>