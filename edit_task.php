<?php
  require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendify | Smart Personal Assistant</title>
    <link rel="stylesheet" href="styles.css">
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
        <section class="header-section">
            <h2>Calendify: Smart Personal Assistant</h2>
            <p>Organize your tasks efficiently</p>
        </section>

        <section class="welcome-section">
            <div class="welcome-message">
                <img src="path-to-avatar.png">
                <?php
                if (isset($_SESSION['username'])) {
                    echo '<p>Welcome, ' . $_SESSION['username'] . '!</p>';
                } 
                ?>
                <p>Welcome to the Calendify!</p>
            </div>
        </section>

        <section class="task-list-section">
            <h3>Task List</h3>
            <p>Manage your tasks efficiently</p>

            <div class="task-buttons">
                <button onclick="window.location.href='edit_task.php'"class="edit-task">Edit Task</button>
                <button onclick="window.location.href='add_task.php'" class="add-task">Add Task</button>
            </div>

            <ul class="task-list">
                <li class="task-item high-priority">
                    <h4>Task 1</h4>
                    <p>Task Description</p>
                    <p>Due Date: 01/01/2024</p>
                    <button class="delete-task">Delete</button>
                </li>
                <li class="task-item medium-priority">
                    <h4>Task 2</h4>
                    <p>Task Description</p>
                    <p>Due Date: 01/01/2024</p>
                    <button class="delete-task">Delete</button>
                </li>
                <li class="task-item low-priority">
                    <h4>Task 3</h4>
                    <p>Task Description</p>
                    <p>Due Date: 01/01/2024</p>
                    <button class="delete-task">Delete</button>
                </li>
            </ul>
        </section>