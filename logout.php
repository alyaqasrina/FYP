<?php
session_start(); // Start the session to access session variables

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['logout'])) {
        session_destroy(); // Destroy the session
        header('Location: login.php'); // Redirect to login page
        exit();
    } elseif (isset($_POST['cancel'])) {
        header('Location: homepage.php'); // Redirect to homepage or another appropriate page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout | Calendify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <img src="logo.png" alt="Calendify Logo">
            <h1>CALENDIFY</h1>
        </div>
        <ul class="nav-links">
            <li><a href="homepage.php">Home</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="setting.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <input type="text" class="search-bar" placeholder="Search in site">
    </nav>

    <!-- Main Content -->
    <main class="content">
        <section class="logout-section">
            <h4>Are you sure you want to logout?</h4>
            <form action="logout.php" method="post">
                <button type="submit" name="logout" class="logout">Logout</button>
                <button type="submit" name="cancel" class="cancel">Cancel</button>
            </form>
        </section>
    </main>

</body>
</html>