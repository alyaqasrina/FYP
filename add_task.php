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
    <section class="add-task">
        <div class="add-task-section">
            <h1 class="add-task-title">Add Task</h1>
            <form action="add_task.php" method="POST">
                <div class="main-task">
                    <h2 class="main-task-title">Main Task</h2>
                    <p class="main-task-description">Enter the details of the main task</p>
                    
                    <div class="form-group"> 
                        <label for="task_name">Task Name:</label>
                        <input type="text" name="task_name" required>
                    </div>

                    <div class="form-group">
                        <label for="due_date">Due Date:</label>
                        <input type="date" name="due_date" required>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority:</label>
                        <select name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                
                <div class="subtask">
                    <h2 class="subtask-title">Subtasks</h2>
                    <p class="subtask-description">Enter the details of the subtasks</p>
                    <div class="subtask-group">
                        <div class="subtask-form-group">
                            <label for="subtask_name">Subtask Name:</label>
                            <input type="text" name="subtask_name[]" required>
                        </div>

                        <div class="subtask-form-group">
                            <label for="subtask_due_date">Subtask Due Date:</label>
                            <input type="date" name="subtask_due_date[]" required>
                        </div>

                        <div class="subtask-form-group">
                            <label for="subtask_priority">Subtask Priority:</label>
                            <select name="subtask_priority[]" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addSubtask()">Add Another Subtask</button>
                        <button type="submit" class="btn btn-primary">Save Task</button>
                    </div>
            </form>

                <script>
                    function addSubtask() {
                        const subtaskGroup = document.querySelector('.subtask-group');
                        const newSubtask = subtaskGroup.cloneNode(true);
                        newSubtask.querySelectorAll('input').forEach(input => input.value = '');
                        document.querySelector('.subtask').appendChild(newSubtask);
                    }
                </script>

                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Get main task details
                    $task_name = $_POST['task_name'];
                    $due_date = $_POST['due_date'];
                    $priority = $_POST['priority'];

                    // Get subtasks details
                    $subtask_names = $_POST['subtask_name'];
                    $subtask_due_dates = $_POST['subtask_due_date'];
                    $subtask_priorities = $_POST['subtask_priority'];

                    // Display the main task
                    echo "<h2>Task Added:</h2>";
                    echo "<strong>Task Name:</strong> $task_name<br>";
                    echo "<strong>Due Date:</strong> $due_date<br>";
                    echo "<strong>Priority:</strong> $priority<br>";

                    // Display subtasks
                    echo "<h3>Subtasks:</h3>";
                    for ($i = 0; $i < count($subtask_names); $i++) {
                        echo "<strong>Subtask Name:</strong> " . htmlspecialchars($subtask_names[$i]) . "<br>";
                        echo "<strong>Subtask Due Date:</strong> " . htmlspecialchars($subtask_due_dates[$i]) . "<br>";
                        echo "<strong>Subtask Priority:</strong> " . htmlspecialchars($subtask_priorities[$i]) . "<br><br>";
                    }
                }
                ?>
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