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
                <?php
                // Fetch tasks from the database
                $query = "SELECT * FROM tasks";
                $result = mysqli_query($conn, $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li>' . $row['task_name'] . '</li>';
                    }
                } else {
                    echo '<p>No tasks found.</p>';
                }

                


                ?>
            </ul>
        </section>
    </main>
</body>
</html>

<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "your_database_name");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the task ID from the query string
if (isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']);
    
    // Fetch task details
    $query = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        die("Task not found.");
    }
} else {
    die("Invalid request.");
}

// Handle form submission for updating the task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $task_due_date = $_POST['task_due_date'];

    // Update task details
    $update_query = "UPDATE tasks SET name = ?, description = ?, due_date = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssi", $task_name, $task_description, $task_due_date, $task_id);

    if ($update_stmt->execute()) {
        echo "Task updated successfully.";
        header("Location: task_table.php"); // Redirect back to the task table
        exit;
    } else {
        echo "Error updating task: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
</head>
<body>
    <h1>Edit Task</h1>
    <form action="edit_task.php?task_id=<?php echo $task_id; ?>" method="post">
        <label for="task_name">Task Name:</label>
        <input type="text" name="task_name" id="task_name" value="<?php echo htmlspecialchars($task['name']); ?>" required><br>

        <label for="task_description">Task Description:</label>
        <textarea name="task_description" id="task_description" required><?php echo htmlspecialchars($task['description']); ?></textarea><br>

        <label for="task_due_date">Due Date:</label>
        <input type="date" name="task_due_date" id="task_due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required><br>

        <button type="submit">Update Task</button>
    </form>
</body>
</html>
