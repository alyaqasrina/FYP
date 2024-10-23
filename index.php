<DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>Calendify</title>
    <!-- Just an image -->
    <nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
        <img src="calendify\logo.png" width="30" height="30" alt="">
    </a>
    </nav>
</head>
<body>
    <section class="index section 1">
        <div class="index-wrapper">
            <div class="index-content">
                <h1 class="index-title">Welcome to Calendify</h1>
                <p class="index-description">Manage your calendar and tasks efficiently</p>
                <h2>New to Calendify?</h2> <button onclick="window.location.href='register.php'" type="button" class="btn btn-primary">Sign up </button> 
                <h2>Already have an account?</h2> <button onclick="window.location.href='login.php'" type="button" class="btn btn-primary" href="login.php">Log in</button> <a href="resetPassword"> Reset password</a>
            </div>
        </div>
    </section>
    <section class="footer">
        <div class="footer-wrapper">
            <div class="footer-content">
            <footer class="footer">
        <div class="footer-section links-section">
            <a href="homepage.php">Home</a>
            <a href="#services">About Us</a>
            <a href="#privacy">Privacy Policy</a>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2024 Calendify. All rights reserved.
    </div>
</footer>

            </div>
        </div>
</html>