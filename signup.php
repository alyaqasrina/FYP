<?php
require('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = stripslashes($_POST['username']);
    $username = mysqli_real_escape_string($conn, $username);
    $phoneNumber = stripslashes($_POST['phone_number']);
    $phoneNumber = mysqli_real_escape_string($conn, $phoneNumber);
    $email = stripslashes($_POST['email']);
    $email = mysqli_real_escape_string($conn, $email);
    $password = stripslashes($_POST['password']);
    $password = mysqli_real_escape_string($conn, $password);
    $confirm_password = stripslashes($_POST['confirm-password']);
    $confirm_password = mysqli_real_escape_string($conn, $confirm_password);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<div class='form'>
              <h3>Passwords do not match.</h3><br/>
              <p class='link'>Click here to <a href='signup'>register</a> again.</p>
              </div>";
    } else {
        // Check if username or email already exists
        $query = "SELECT * FROM `users` WHERE username='$username' OR email='$email'";
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        $rows = mysqli_num_rows($result);

        if ($rows > 0) {
            echo "<div class='form'>
                  <h3>Username or email already exists.</h3><br/>
                  <p class='link'>Click here to <a href='signup'>register</a> again.</p>
                  </div>";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database
            $query = "INSERT INTO `users` (username, email, password, phone_number) 
                      VALUES ('$username', '$email', '$hashed_password', '$phoneNumber')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                echo "<div class='form'>
                      <h3>You are registered successfully.</h3><br/>
                      <p class='link'>Click here to <a href='login'>login</a></p>
                      </div>";
            } else {
                echo "<div class='form'>
                      <h3>Registration failed. Please try again.</h3><br/>
                      <p class='link'>Click here to <a href='signup'>register</a> again.</p>
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
    <title>Signup - Calendify</title>
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="signup-container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Sign Up</h3></div>
                    <div class="card-body">
                        <form action="signup" method="POST">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputUsername" name="username" type="text" placeholder="Username" required />
                                <label for="inputUsername">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required />
                                <label for="inputEmail">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPhoneNumber" name="phone_number" type="text" placeholder="Phone Number" required />
                                <label for="inputPhoneNumber">Phone Number</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                <label for="inputPassword">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputConfirmPassword" name="confirm-password" type="password" placeholder="Confirm Password" required />
                                <label for="inputConfirmPassword">Confirm Password</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <button class="btn btn-primary" type="submit">Sign Up</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <div class="small"><a href="login">Have an account? Go to login</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>