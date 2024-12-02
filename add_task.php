<?php
session_start();
require('db.php'); // Ensure db.php contains the correct database connection code.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize main task inputs
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    // Insert main task into the database
    $query = "INSERT INTO `tasks` (task_name, description, due_date, priority) 
              VALUES ('$task_name', '$task_description', '$due_date', '$priority')";
    if (mysqli_query($conn, $query)) {
        $task_id = mysqli_insert_id($conn); // Get the ID of the inserted task

        // Handle subtasks
        if (isset($_POST['subtask_name'])) {
            $subtask_names = $_POST['subtask_name'];
            $subtask_due_dates = $_POST['subtask_due_date'];
            $subtask_priorities = $_POST['subtask_priority'];

            foreach ($subtask_names as $index => $subtask_name) {
                // Validate and sanitize subtask inputs
                $subtask_name = mysqli_real_escape_string($conn, $subtask_name);
                $subtask_due_date = mysqli_real_escape_string($conn, $subtask_due_dates[$index]);
                $subtask_priority = mysqli_real_escape_string($conn, $subtask_priorities[$index]);

                // Insert subtask into the database
                $subtask_query = "INSERT INTO `subtasks` (task_id, subtask_name, subtask_description, subtask_due_date, subtask_priority) 
                                  VALUES ('$task_id', '$subtask_name', '$subtask_due_date', '$subtask_priority')";
                mysqli_query($conn, $subtask_query);
            }
        }

        echo "<div class='form'>
              <h3>Task and subtasks added successfully.</h3><br/>
              <p class='link'>Click here to <a href='tasks.php'>view tasks</a></p>
              </div>";
    } else {
        echo "<div class='form'>
              <h3>Error: Could not add task.</h3><br/>
              <p class='link'>Click here to <a href='add_task.php'>try again</a>.</p>
              </div>";
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
                <div class="add-task-container">
                    <div class="main-task-form">
                        <h2 class="main-task-title">Main Task</h2>
                        
                        <div class="main-task-form-group"> 
                            <label for="task_name">Task Name:</label>
                            <input type="text" name="task_name" required>
                        </div>

                        <div class="main-task-form-group">
                            <label for="task_description">Task Description:</label>
                            <textarea name="task_description" required></textarea>
                        </div>

                        <div class="main-task-form-group">
                            <label for="due_date">Due Date:</label>
                            <input type="date" name="due_date" required>
                        </div>

                        <div class="main-task-form-group">
                            <label for="priority" class="task-priority">Priority:</label>
                            <select name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="subtask-form">
                        <h2 class="subtask-title">Subtasks</h2>

                        <div class="subtask-group">
                            <div class="subtask-form-group">
                                <label for="subtask_name">Subtask Name:</label>
                                <input type="text" name="subtask_name[]" required>
                            </div>

                            <div class="subtask-form-group">
                                <label for="subtask_due_date">Due Date:</label>
                                <input type="date" name="subtask_due_date[]" required>
                            </div>

                            <div class="subtask-form-group">
                                <label for="subtask_priority" class="task-priority">Priority:</label>
                                <select name="subtask_priority[]" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="subtask-button-save" onclick="addSubtask()">Add Another Subtask</button>
                    </div>
                </div>
                <button type="submit" class="save-task-button">Save Task</button>
            </form>
        </div>
    </section>

    <script>
        function addSubtask() {
            const subtaskGroup = document.querySelector('.subtask-group');
            const newSubtask = subtaskGroup.cloneNode(true);
            newSubtask.querySelectorAll('input').forEach(input => input.value = '');
            newSubtask.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
            newSubtask.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            document.querySelector('.subtask-form').appendChild(newSubtask);
        }
    </script>
    
    <?php
        function getPriorityClass($priority) {
            switch (strtolower($priority)) {
                case 'low':
                    return 'low-priority';
                case 'medium':
                    return 'medium-priority';
                case 'high':
                    return 'high-priority';
                default:
                    return '';
            }

        function editTask($task_name) {
            echo "<script>alert('Edit Task: {$task_name}');</script>";
            // Redirect to an edit form or load data into existing form
            echo "<script>window.location.href = 'edit_task.php?task_name={$task_name}';</script>";
        }
        
        function deleteTask($task_name) {
            echo "<script>
                if(confirm('Are you sure you want to delete the task: {$task_name}?')) {
                    window.location.href = 'delete_task.php?task_name={$task_name}';
                }
            </script>";
        }
        
        function editSubtask($subtask_name) {
            echo "<script>alert('Edit Subtask: {$subtask_name}');</script>";
            // Redirect to an edit form or load data into existing form
            echo "<script>window.location.href = 'edit_subtask.php?subtask_name={$subtask_name}';</script>";
        }
        
        function deleteSubtask($subtask_name) {
            echo "<script>
                if(confirm('Are you sure you want to delete the subtask: {$subtask_name}?')) {
                    window.location.href = 'delete_subtask.php?subtask_name={$subtask_name}';
                }
            </script>";
        }       
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get main task details
        $task_name = htmlspecialchars($_POST['task_name']);
        $task_description = htmlspecialchars($_POST['task_description']);
        $due_date = htmlspecialchars($_POST['due_date']);
        $priority = htmlspecialchars($_POST['priority']);

        // Get subtasks details
        $subtask_names = $_POST['subtask_name'] ?? [];
        $subtask_due_dates = $_POST['subtask_due_date'] ?? [];
        $subtask_priorities = $_POST['subtask_priority'] ?? [];

        // Display the main task
        echo "<h2>Tasks:</h2>";
        echo "<table class='task-table'>";
        echo "<thead>
                <tr>
                    <th>Task Name</th>
                    <th>Task Description</th>
                    <th>Due Date</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>";
        echo "<tbody>";
        echo "<tr>
                <td>{$task_name}</td>
                <td>{$task_description}</td>
                <td>{$due_date}</td>
                <td class='" . getPriorityClass($priority) . "'>{$priority}</td>
                <td>
                    <button onclick=\"editTask('$task_name')\" style='border: none; background: none; cursor: pointer;'>
                        <img src='edit.png' alt='Edit' style='width: 25px; height: 25px;'>
                    </button>
                    <button onclick=\"deleteTask('$task_name')\" style='border: none; background: none; cursor: pointer;'>
                        <img src='trash.png' alt='Delete' style='width: 30px; height: 30px;'>
                    </button>
                </td>
            </tr>";
        echo "</tbody>";
        echo "</table>";

        echo "<h3>Subtasks:</h3>";
        echo "<table class='task-table'>";
        echo "<thead>
                <tr>
                    <th>Subtask Name</th>
                    <th>Subtask Description</th>
                    <th>Subtask Due Date</th>
                    <th>Subtask Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>";
        echo "<tbody>";
        foreach ($subtask_names as $index => $subtask_name) {
            $subtask_due_date = htmlspecialchars($subtask_due_dates[$index] ?? '');
            $subtask_priority = htmlspecialchars($subtask_priorities[$index] ?? '');
            echo "<tr>
                    <td>{$subtask_name}</td>
                    <td>{$subtask_description}</td>
                    <td>{$subtask_due_date}</td>
                    <td class='" . getPriorityClass($subtask_priority) . "'>{$subtask_priority}</td>
                    <td>
                    <button onclick=\"editSubtask('$subtask_name')\" style='border: none; background: none; cursor: pointer;'>
                        <img src='edit.png' alt='Edit' style='width: 25px; height: 25px;'>
                    </button>
                    <button onclick=\"deleteSubtask('$subtask_name')\" style='border: none; background: none; cursor: pointer;'>
                        <img src='trash.png' alt='Delete' style='width: 30px; height: 30px;'>
                    </button>
                    </td>
                </tr>";
        }
        echo "</tbody>";
        echo "</table>";
        }
    ?>

</body>
</html>