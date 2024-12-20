<?php
session_start();
include('db.php'); // Ensure this connects properly to the database

// Function to determine priority class
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
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Error: User is not logged in.");
    }

    $user_id = $_SESSION['user_id'];

    // Validate and sanitize inputs
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    // Insert main task into the database
    $query = "INSERT INTO `tasks` (task_name, description, due_date, priority, user_id) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $task_name, $task_description, $due_date, $priority, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $task_id = mysqli_insert_id($conn); // Get the inserted task's ID

        // Handle subtasks if provided
        if (!empty($_POST['subtask_name'])) {
            $subtask_names = $_POST['subtask_name'];
            $subtask_due_dates = $_POST['subtask_due_date'];
            $subtask_priority = $_POST['subtask_priority'];
        
            $subtask_query = "INSERT INTO `subtasks` (task_id, subtask_name, subtask_due_date, subtask_priority) 
                              VALUES (?, ?, ?, ?)";
            $subtask_stmt = mysqli_prepare($conn, $subtask_query);
        
            foreach ($subtask_names as $index => $subtask_name) {
                $subtask_name = mysqli_real_escape_string($conn, $subtask_name);
                $subtask_due_date = mysqli_real_escape_string($conn, $subtask_due_dates[$index]);
                $subtask_priority = mysqli_real_escape_string($conn, $subtask_priority[$index]);
        
                if (empty($subtask_name) || empty($subtask_due_date) || empty($subtask_priority)) {
                    die("Error: All subtask fields are required.");
                }
        
                mysqli_stmt_bind_param($subtask_stmt, "isss", $task_id, $subtask_name, $subtask_due_date, $subtask_priority);
                mysqli_stmt_execute($subtask_stmt);
            }
        }
        
        // Display the main task and subtasks
        echo "<h2>Task Added:</h2>";
        echo "<table>";
        echo "<tr><th>Task Name</th><th>Task Description</th><th>Due Date</th><th>Priority</th></tr>";
        echo "<tr><td>$task_name</td><td>$task_description</td><td>$due_date</td><td class='task-priority " . getPriorityClass($priority) . "'>$priority</td></tr>";
        echo "</table>";

        echo "<h3>Subtasks:</h3>";
        echo "<table>";
        echo "<tr><th>Subtask Name</th><th>Subtask Due Date</th><th>Subtask Priority</th></tr>";
        for ($i = 0; $i < count($subtask_names); $i++) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($subtask_names[$i]) . "</td>";
            echo "<td>" . htmlspecialchars($subtask_due_dates[$i]) . "</td>";
            echo "<td class='task-priority " . getPriorityClass($subtask_priority[$i]) . "'>" . htmlspecialchars($subtask_priority[$i]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Task added successfully!</p>";
        echo "<button onclick='window.location.href=\"homepage.php\"' class='btn btn-primary'>Go Back</button>";
    } else {
        echo "<p>Error adding task: " . mysqli_error($conn) . "</p>";
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
                        <p class="main-task-description">Enter the details of the main task</p>
                        
                        <div class="main-task-form-group"> 
                            <label for="task_name">Task Name:</label>
                            <input type="text" name="task_name" required>
                        </div>

                        <div class="main-task-form-group">
                            <label for="description">Task Description:</label>
                            <textarea name="description" required></textarea>
                        </div>

                        <div class="main-task-form-group">
                            <label for="due_date">Due Date:</label>
                            <input type="date" name="due_date" required>
                        </div>

                        <div class="main-task-form-group">
                            <label for="priority" class="task-priority">Priority:</label>
                            <select name="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="subtask-form">
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
                                <label for="subtask_status" class="task-priority">Subtask Priority:</label>
                                <select name="subtask_priority[]" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addSubtask()">Add Another Subtask</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Task</button>
            </form>
        </div>
    </section>

    <script>
        function addSubtask() {
        const subtaskForm = document.querySelector('.subtask-form');
        const subtaskGroup = document.querySelector('.subtask-group');
        const newSubtask = subtaskGroup.cloneNode(true);

        // Clear inputs
        newSubtask.querySelectorAll('input, select').forEach((input) => {
            input.value = '';
            const name = input.getAttribute('name');
            if (name) {
                const indexMatch = name.match(/\[\d+\]/);
                if (indexMatch) {
                    const newIndex = document.querySelectorAll('.subtask-group').length;
                    const newName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                    input.setAttribute('name', newName);
                }
            }
        });

        subtaskForm.appendChild(newSubtask);
        }

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
        }

    </script>
</body>
</html>