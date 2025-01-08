<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch prioritized tasks for today
$user_id = $_SESSION['user_id']; // Ensure the user ID is available
$query = "SELECT 
            t.task_id,
            t.task_name AS title,
            t.priority AS priority_level,
            t.due_date AS due_time
          FROM tasks t
          WHERE t.user_id = ? AND DATE(t.due_date) = CURDATE()";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Calendify</title>
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
                        <a class="nav-link" href="homepage.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div> Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Task</div>
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div> Overview
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse show" id="collapseLayouts">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="task.php">Task</a>
                                <a class="nav-link" href="monitor_status.php">Monitor Status</a>
                                <a class="nav-link" href="calendar.php">Calendar</a>
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
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Your Overall Task</h1>

                    <!-- Prioritized Tasks Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-tasks me-1"></i> Today's Prioritized Tasks
                        </div>
                        <div class="card-body">
                            <ul id="prioritized-task-list" class="list-group">
                                <li class="list-group-item">Loading prioritized tasks...</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Task Summary Cards -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">High Priority
                                    <?php
                                    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE priority = 'High' AND user_id = $user_id";
                                    $result = mysqli_query($conn, $sql);
                                    $row = mysqli_fetch_assoc($result);
                                    echo "<h2>" . $row['total'] . "</h2>";
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Medium Priority
                                    <?php
                                    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE priority = 'Medium' AND user_id = $user_id";
                                    $result = mysqli_query($conn, $sql);
                                    $row = mysqli_fetch_assoc($result);
                                    echo "<h2>" . $row['total'] . "</h2>";
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Low Priority
                                    <?php
                                    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE priority = 'Low' AND user_id = $user_id";
                                    $result = mysqli_query($conn, $sql);
                                    $row = mysqli_fetch_assoc($result);
                                    echo "<h2>" . $row['total'] . "</h2>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task List -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i> Task List
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Priority</th>
                                        <th>Task Due Date</th>
                                        <th>Subtasks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Update SQL query to use GROUP_CONCAT for subtasks
                                    $query = "SELECT 
                                                    t.task_id, 
                                                    t.task_name, 
                                                    t.priority, 
                                                    t.due_date AS task_due_date, 
                                                    GROUP_CONCAT(CONCAT(s.subtask_name, ' (Due: ', s.subtask_due_date, ')') SEPARATOR '||') AS subtasks
                                                FROM tasks t
                                                LEFT JOIN subtasks s ON t.task_id = s.task_id
                                                WHERE t.user_id = $user_id
                                                GROUP BY t.task_id";

                                    $result = mysqli_query($conn, $query);

                                    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                    foreach ($tasks as $task) {
                                        echo '<tr>';
                                        echo '<td>' . $task['task_name'] . '</td>';
                                        echo '<td>' . $task['priority'] . '</td>';
                                        echo '<td>' . $task['task_due_date'] . '</td>';

                                        // Generate bullet list for subtasks
                                        if ($task['subtasks']) {
                                            $subtasks = explode('||', $task['subtasks']);
                                            echo '<td><ul>';
                                            foreach ($subtasks as $subtask) {
                                                echo '<li>' . $subtask . '</li>';
                                            }
                                            echo '</ul></td>';
                                        } else {
                                            echo '<td>No subtasks</td>';
                                        }

                                        echo '<td>
                                                    <a href="edit_task.php?task_id=' . $task['task_id'] . '" class="btn btn-primary btn-sm">Edit</a>
                                                    <a href="delete_task.php?task_id=' . $task['task_id'] . '" class="btn btn-danger btn-sm">Delete</a>
                                                </td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="add-task">
                        <button onclick="window.location.href='add_task.php'" class="btn btn-primary">Add Task</button>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; 2024 Calendify. All rights reserved</div>
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('prioritize_task.php') // Fetch data from the backend
                .then(response => response.json())
                .then(data => {
                    const taskList = document.getElementById('prioritized-task-list');
                    taskList.innerHTML = ''; // Clear placeholder

                    if (data && data.length > 0) {
                        // Populate prioritized tasks
                        data.forEach(task => {
                            const listItem = document.createElement('li');
                            listItem.className = 'list-group-item';
                            listItem.innerHTML = `
                            <strong>${task.title}</strong>
                            <br> Priority: ${task.priority_level} | Due: ${task.due_time} | Score: ${Math.round(task.total_score)}
                        `;
                            taskList.appendChild(listItem);
                        });
                    } else {
                        taskList.innerHTML = '<li class="list-group-item">No high-priority tasks for today!</li>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching prioritized tasks:', error);
                    const taskList = document.getElementById('prioritized-task-list');
                    taskList.innerHTML = '<li class="list-group-item text-danger">Failed to load prioritized tasks.</li>';
                });
        });
    </script>

</body>

</html>