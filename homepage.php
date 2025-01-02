<?php
session_start();
include('db.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Fetch prioritized tasks
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM tasks WHERE user_id = ? AND due_date <= CURDATE() AND priority IN ('High', 'Medium', 'Low')";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Calendify</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="homepage.php">
            <img src="path/to/logo.png" style="height: 30px; width: auto;">
            Calendify
        </a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php">Setting</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">
                            <Main></Main>
                        </div>
                        <a class="nav-link" href="homepage.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Task</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Overview
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
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
                    <?php
                    echo $_SESSION['username'];
                    ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">View your task for today</li>
                    </ol>

                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">Today's High Priority Tasks
                                    <?php
                                    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE priority = 'High' AND due_date = CURDATE() AND user_id = ?";
                                    $stmt = mysqli_prepare($conn, $sql);
                                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $row = mysqli_fetch_assoc($result);
                                    echo "<h2>" . $row['total'] . "</h2>";
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Today's Medium Priority Tasks
                                    <?php
                                    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE priority = 'Medium' AND due_date = CURDATE() AND user_id = ?";
                                    $stmt = mysqli_prepare($conn, $sql);
                                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $row = mysqli_fetch_assoc($result);
                                    echo "<h2>" . $row['total'] . "</h2>";
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Today's Low Priority Tasks
                                    <?php
                                    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE priority = 'Low' AND due_date = CURDATE() AND user_id = ?";
                                    $stmt = mysqli_prepare($conn, $sql);
                                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $row = mysqli_fetch_assoc($result);
                                    echo "<h2>" . $row['total'] . "</h2>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-tasks me-1"></i>
                            Today's Prioritized Tasks
                        </div>
                        <div class="card-body">
                            <!-- <ul id="prioritized-task-list" class="list-group">
                                <li class="list-group-item">Loading prioritized tasks...</li>
                            </ul> -->

                            <ul id="prioritized-task-list" class="list-group">
                                <li class="list-group-item">Task 1</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Task List
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="datatable-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Task Due Date</th>
                                        <th>Subtask </th>
                                        <th>Subtask Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        // Fetch tasks from the database
                                        $query = "SELECT * FROM tasks WHERE due_date = CURDATE()";
                                        $result = mysqli_query($conn, $query);
                                        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                        //Fetch subtasks from the database
                                        $query = "SELECT * FROM subtasks WHERE subtask_due_date = CURDATE()";
                                        $result = mysqli_query($conn, $query);
                                        $subtasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                        // Display the tasks in the table
                                        foreach ($tasks as $task) {
                                            echo '<tr>';
                                            echo '<td>' . $task['task_name'] . '</td>';
                                            echo '<td>' . $task['due_date'] . '</td>';
                                            echo '<td>' . $subtasks['subtask_name'] . '</td>';
                                            echo '<td>' . $subtasks['subtask_due_date'] . '</td>';
                                            echo '<td><a href="edit_task.php?id=' . $task['id'] . '" img>Edit</a></td>';
                                            echo '<td><a href="delete_task.php?id=' . $task['id'] . '">Delete</a></td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>

</body>

</html>