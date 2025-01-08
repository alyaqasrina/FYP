<?php
session_start();
include('db.php'); // Ensure this connects properly to the database

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in.");
}

$user_id = $_SESSION['user_id'];

// Retrieve task and subtasks data
if (isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']);

    // Fetch main task details
    // Prepare a SQL query to select a task based on task_id and user_id
    $task_query = "SELECT * FROM `tasks` WHERE `task_id` = ? AND `user_id` = ?";

    // Initialize a prepared statement with the SQL query
    $stmt = mysqli_prepare($conn, $task_query);

    // Bind the task_id and user_id parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);

    // Execute the prepared statement
    mysqli_stmt_execute($stmt);

    // Fetch the result of the executed statement as an associative array
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$task) {
        die("Error: Task not found or you do not have access to it.");
    }

    // Fetch subtasks
    $subtask_query = "SELECT * FROM `subtasks` WHERE `task_id` = ?";
    $stmt = mysqli_prepare($conn, $subtask_query);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);
    $subtasks_result = mysqli_stmt_get_result($stmt);
    $subtasks = mysqli_fetch_all($subtasks_result, MYSQLI_ASSOC);
} else {
    die("Error: Task ID is required.");
}

// Handle form submission for deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete subtasks first
    $delete_subtasks_query = "DELETE FROM `subtasks` WHERE `task_id` = ?";
    $stmt = mysqli_prepare($conn, $delete_subtasks_query);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);

    // Delete the main task
    $delete_task_query = "DELETE FROM `tasks` WHERE `task_id` = ? AND `user_id` = ?";
    $stmt = mysqli_prepare($conn, $delete_task_query);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Success message and redirect
        echo "<script>
            alert('Task and its subtasks deleted successfully!');
            window.location.href = 'task.php';
        </script>";
        exit;
    } else {
        die("Error deleting task: " . mysqli_error($conn));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Delete Task</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <?php include('calendify_brand.php') ?>

        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <?php include('profile_navbar.php') ?>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="homepage">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Task</div>
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Overview
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse show" id="collapseLayouts">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="task">Task</a>
                                <a class="nav-link" href="monitor_status">Monitor Status</a>
                                <a class="nav-link" href="calendar">Calendar</a>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo $_SESSION['username']; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="delete-task-container">
                    <div class="delete-task-header">
                        <h2>Are you sure you want to delete this task?</h2>
                        <p class="text-muted">This action will permanently delete the task and its subtasks.</p>
                    </div>
                    <div class="task-section">
                        <div class="task-box">
                            <h3>Main Task:</h3>
                            <p><strong>Task Name:</strong> <?= htmlspecialchars($task['task_name']) ?></p>
                            <p><strong>Description:</strong> <?= htmlspecialchars($task['description']) ?></p>
                            <p><strong>Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></p>
                            <p><strong>Priority:</strong> <?= htmlspecialchars($task['priority']) ?></p>
                        </div>
                        <div class="task-box">
                            <h3>Subtasks:</h3>
                            <ul>
                                <?php foreach ($subtasks as $subtask): ?>
                                    <li>
                                        <strong>Name:</strong> <?= htmlspecialchars($subtask['subtask_name']) ?> |
                                        <strong>Due Date:</strong> <?= htmlspecialchars($subtask['subtask_due_date']) ?> |
                                        <strong>Priority:</strong> <?= htmlspecialchars($subtask['subtask_priority']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <form action="delete_task?task_id=<?= $task_id ?>" method="POST" style="display: inline;">
                            <button type="submit" class="btn btn-danger">Delete Task</button>
                        </form>
                        <a href="task" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted"> &copy; 2024 Calendify. All rights reserved</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>