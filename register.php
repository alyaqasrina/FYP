<style> 

/* Set background color for the body */
body {
    background-color: #f0f2f5;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Style the signup form */
.signup-form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

/* Style the form elements */
.signup-form .form-group {
    margin-bottom: 15px;
    text-align: left;
}

.signup-form label {
    display: block;
    margin-bottom: 5px;
}

.signup-form input[type="text"],
.signup-form input[type="email"],
.signup-form input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Style the buttons */
.signup-form .btn {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

.signup-form .btn:hover {
    background-color: #0056b3;
}

</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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
              <p class='link'>Click here to <a href='register.php'>register</a> again.</p>
              </div>";
    } else {
        // Check if username or email already exists
        $query = "SELECT * FROM `users` WHERE username='$username' OR email='$email'";
        $result = mysqli_query($conn, $query) or die(mysqli_error($con));
        $rows = mysqli_num_rows($result);

        if ($rows > 0) {
            echo "<div class='form'>
                  <h3>Username or email already exists.</h3><br/>
                  <p class='link'>Click here to <a href='register.php'>register</a> again.</p>
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
                      <p class='link'>Click here to <a href='register.php'>register</a> again.</p>
                      </div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Sign Up</title>
    <!-- Just an image -->
    <nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
        <img src="calendify\logo.png" width="30" height="30" alt="">
    </a>
    </nav>
</head>
<body>
    <div class="signup-form">
        <h2>Sign Up</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required>
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
            <button type="button" class="btn" onclick="window.location.href='login.php'">Already have an account? Login here</button>
            <button type="submit" class="btn">Sign Up</button>
        </form>
    </div>
</body>
</html>