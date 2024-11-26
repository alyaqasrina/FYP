<?php
    session_start(); // Start the session
?>

<DOCTYPE html>
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
    <div class="container">
    <section class="index-section-welcome">
        <div class="index-wrapper">
            <div class="index-content-title">
                <h1 class="index-title">Welcome to Calendify</h1>
                <p class="index-title-description">Manage your calendar and tasks efficiently</p>
            </div>
        </div>   
    </section>

   <section class="index-section-description">
    <div class="index-wrapper">
        <div class="index-content-description">
            <h2>New to Calendify?</h2>
            <button onclick="window.location.href='signup.php'" type="button" class="btn btn-primary">Sign up</button>
        </div>
        <div class="index-content-description">
            <h2>Already have an account?</h2>
            <button onclick="window.location.href='login.php'" type="button" class="btn btn-primary">Log in</button>
            <button onclick="window.location.href='resetPassword.php'" type="button" class="btn btn-primary">Reset Password</button>
        </div>
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
    </div>
</body>
</html>






