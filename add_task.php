<?php
require('db.php');
session_start();
$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Check if the user ID exists in the users table
$userCheckSql = "SELECT user_id FROM users WHERE user_id = '$user_id'";
$userCheckResult = $conn->query($userCheckSql);
if ($userCheckResult->num_rows == 0) {
    die("Invalid user ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendify | Smart Personal Assistant</title>
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
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <input type="text" class="search-bar" placeholder="Search in site">
    </nav>

    <!-- Main Content -->
    <main class="content">
        <section class="header-section">
            <h2>Add task</h2>
        </section>

        <?php
        require('db.php');
        // Check if $conn is defined
        if (!$conn) {
            die("Database connection failed.");
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST ['user_id']) && isset($_POST ['task_name']) && isset($_POST['description']) && isset($_POST['due_date']) && isset($_POST['priority'])) {
                $user_id = stripslashes($_POST['user_id']);
                $user_id = mysqli_real_escape_string($conn, $user_id);
                $task_name = stripslashes($_POST['task_name']);
                $task_name = mysqli_real_escape_string($conn, $task_name);
                $description = stripslashes($_POST['description']);
                $description = mysqli_real_escape_string($conn, $description);
                $due_date = stripslashes($_POST['due_date']);
                $due_date = mysqli_real_escape_string($conn, $due_date);
                $priority = stripslashes($_POST['priority']);
                $priority = mysqli_real_escape_string($conn, $priority);

                // Insert the main task into the tasks table
                $taskSql = "INSERT INTO tasks (user_id, task_name, description, due_date, priority) 
                            VALUES ('$user_id', '$task_name', '$description', '$due_date', '$priority')";
                if ($conn->query($taskSql) === TRUE) {
                    $taskId = $conn->insert_id;

                    // Insert subtasks if they exist
                    if (isset($_POST['subtask_name'])) {
                        $subtaskTitles = $_POST['subtask_name'];
                        $subtaskDueDates = $_POST['subtask_due_date'];
                        $subtaskPriorities = $_POST['subtask_priority'];
                        $subtaskStatuses = $_POST['subtask_status'];

                        for ($i = 0; $i < count($subtaskTitles); $i++) {
                            if (!empty($subtaskTitles[$i])) {
                                // Only insert the subtask if the subtask name is not empty
                                $subtaskTitle = stripslashes($subtaskTitles[$i]);
                                $subtaskTitle = mysqli_real_escape_string($conn, $subtaskTitle);
                                $subtaskDueDate = stripslashes($subtaskDueDates[$i]);
                                $subtaskDueDate = mysqli_real_escape_string($conn, $subtaskDueDate);
                                $subtaskPriority = stripslashes($subtaskPriorities[$i]);
                                $subtaskPriority = mysqli_real_escape_string($conn, $subtaskPriority);
                                $subtaskStatus = stripslashes($subtaskStatuses[$i]);
                                $subtaskStatus = mysqli_real_escape_string($conn, $subtaskStatus);

                                $subtaskSql = "INSERT INTO subtasks (task_id, subtask_name, subtask_due_date, subtask_priority, subtask_status) 
                                            VALUES ('$taskId', '$subtaskTitle', '$subtaskDueDate', '$subtaskPriority', '$subtaskStatus')";
                                if ($conn->query($subtaskSql) === FALSE) {
                                    echo "Error: " . $subtaskSql . "<br>" . $conn->error;
                                }
                            }
                        }
                    }
                    echo "Task and subtasks added successfully!";
                } else {
                    echo "Error: " . $taskSql . "<br>" . $conn->error;
                }
            } else {
                echo 'Please fill all the fields';
            }
        }
        ?>
        <section class="task-form-section">
            <h2>Add Task</h2>
            <form action="add_task.php" method="post">

                <!-- Main Task Information -->
                <div class="form-group">
                    <label for="task_name">Task Name</label>
                    <input type="text" id="task_name" name="task_name" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" required>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" required>
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority">
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

            <h2>Add Subtask</h2>

                <!-- Subtask Information -->
                <div class="form-group">
                    <label for="subtask_name[]">Subtask Name</label>
                    <input type="text" id="subtask_name" name="subtask_name[]" placeholder="Enter Subtask Title">
                </div>

                <div class="form-group">
                    <label for="subtask_due_date[]">Subtask Due Date</label>
                    <input type="date" id="subtask_due_date" name="subtask_due_date[]">
                </div>

                <div class="form-group">
                    <label for="subtask_priority[]">Priority</label>
                    <select id="subtask_priority" name="subtask_priority[]">
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subtask_status[]">Status</label>
                    <select id="subtask_status" name="subtask_status[]">
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                <!-- Add more subtasks dynamically -->
                <div id="subtasks-container"></div>

                <button type="button" onclick="addSubtask()">Add Another Subtask</button>

                <!-- Submit Button -->
                <button type="submit">Save Task</button>

            </form>

            <script>
                // Function to dynamically add more subtask fields
                function addSubtask() {
                    const container = document.getElementById('subtasks-container');

                    const subtaskDiv = document.createElement('div');
                    subtaskDiv.innerHTML = `
                        <div class="form-group">
                            <label for="subtask_name[]">Subtask Name</label>
                            <input type="text" name="subtask_name[]" placeholder="Enter Subtask Title" required>
                        </div>

                        <div class="form-group">
                            <label for="subtask_due_date[]">Subtask Due Date</label>
                            <input type="date" name="subtask_due_date[]" required>
                        </div>

                        <div class="form-group">
                            <label for="subtask_priority[]">Priority</label>
                            <select name="subtask_priority[]">
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subtask_status[]">Status</label>
                            <select name="subtask_status[]">
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    `;

                    container.appendChild(subtaskDiv);
                }
            </script>
        </section>
    </main>
</body>
</html>