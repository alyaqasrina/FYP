<?php
    session_start(); // Start the session to access session variables
    require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css" />
    <title>Calendify</title> 
    <nav class="navbar-homepage">
        <div class="logo">
            <img src="logo_new.png" alt="Calendify Logo" href="index.php">
            <h3 class="navbar-title">CALENDIFY</h3>
        </div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="tasks.php">Tasks</a>
            <a href="calendar.php">Calendar</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
        <input type="text" class="search-bar" placeholder="Search">
    </nav>
</head>
<body>

    <!-- Main Content -->
    <section class="homepage">
        <section class="header-section-homepage">
            <h2 class="index-title">Calendify: Smart Personal Assistant</h2>
            <p class="index-title-description">Organize your tasks efficiently</p>
        </section>

        <section class="welcome-section">
            <div class="welcome-message">
                <img src="avatar.png" class="avatar-image" alt="Avatar">
                <h3>
                    Welcome, 
                    <?php 
                    // Check if the username is set in the session
                    if (isset($_SESSION['username'])) {
                        echo htmlspecialchars($_SESSION['username']); // Use htmlspecialchars to prevent XSS
                    } else {
                        echo "Guest"; // Or handle it in another way if not logged in
                    }
                    ?>!
                </h3>
            </div>
        </section>

        <section class="task-list-section">
            <div class="task-wrapper">
                <div class="task-content-title">
                    <h2 class="task-title">Task List</h2>
                    <p class="task-title-description">Here are your tasks for today:</p>
                    <button onclick="window.location.href='add_task.php'" type="button" class="btn btn-primary">Add Task</button>
                </div>
                <div class="task-table">
                    <table>
                        <tr>
                            <th>Task Name</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                        <?php
                        // Fetch tasks from the database
                        $query = "SELECT * FROM tasks";
                        $result = mysqli_query($conn, $query);
                        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        // Display the tasks in the table
                        foreach ($tasks as $task) {
                            echo '<tr>';
                            echo '<td>' . $task['task_name'] . '</td>';
                            echo '<td>' . $task['due_date'] . '</td>';
                            echo '<td>' . $task['priority'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>
        </section>

    <!-- Footer -->
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
