<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Determine the page range (first half or second half)
$page = isset($_GET['page']) && $_GET['page'] == 'second-half' ? 'second-half' : 'first-half';
$start_month = $page === 'second-half' ? 7 : 1;
$end_month = $start_month + 5;
$current_year = 2024; // Adjust dynamically if needed

// Fetch tasks for the selected months
$sql = "SELECT * FROM tasks 
        WHERE DATE_FORMAT(due_date, '%m') BETWEEN ? AND ?
          AND DATE_FORMAT(due_date, '%Y') = ?
        ORDER BY due_date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $start_month, $end_month, $current_year);
$stmt->execute();
$result = $stmt->get_result();

$tasks_by_month = [];
while ($row = $result->fetch_assoc()) {
    $month_name = date("F", strtotime($row['due_date'])); // Convert task_date to month name
    $tasks_by_month[$month_name][] = $row;
}
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
            <?php include('calendify_brand.php') ?>

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
             <?php include('profile_navbar.php') ?>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading"><Main></Main></div>
                            <a class="nav-link" href="homepage">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Task</div>
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Overview
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse show" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
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
                        <?php
                            echo $_SESSION['username'];
                        ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Calendar</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item-active">View your calendar</li>
                        </ol>
                        <div class="row">
                        <div class="col-xl-3 col-md-6"></div>
                        <div class="filter-container d-flex justify-content align-items-center mb-4">
                            <!-- View Selection Dropdown -->
                            <div>
                                <label for="view">View:</label>
                                <select id="view" class="form-select" onchange="updateFilters()">
                                    <option value="monthly" <?= isset($_GET['view']) && $_GET['view'] === 'monthly' ? 'selected' : '' ?>>Monthly View</option>
                                    <option value="yearly" <?= isset($_GET['view']) && $_GET['view'] === 'yearly' ? 'selected' : '' ?>>Yearly View</option>
                                    <option value="weekly" <?= isset($_GET['view']) && $_GET['view'] === 'weekly' ? 'selected' : '' ?>>Weekly View</option>
                                </select>
                            </div>

                            <!-- Priority Selection Dropdown -->
                            <div>
                                <label for="priority">Priority:</label>
                                <select id="priority" class="form-select" onchange="updateFilters()">
                                    <option value="" <?= !isset($_GET['priority']) || $_GET['priority'] === '' ? 'selected' : '' ?>>All Priorities</option>
                                    <option value="high" <?= isset($_GET['priority']) && $_GET['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="medium" <?= isset($_GET['priority']) && $_GET['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="low" <?= isset($_GET['priority']) && $_GET['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="calendar-container">
                            <?php foreach (range($start_month, $end_month) as $month_num): ?>
                            <?php 
                            $month_name = date("F", mktime(0, 0, 0, $month_num, 1)); // Get month name
                            ?>
                            <div class="month">
                                <h3><?= $month_name ?></h3>
                                <?php if (!empty($tasks_by_month[$month_name])): ?>
                                    <?php foreach ($tasks_by_month[$month_name] as $task): ?>
                                        <div class="task">
                                            <!-- <a href="tasks?id=<?= $task['task_id'] ?>"> -->
                                                <h4>
                                                <?= $task['task_name'] ?>
                                                </h4>
                                                <p><?= $task['description'] ?></p>
                                                <small><?= date("d M Y", strtotime($task['due_date'])) ?></small>
                                            <!-- </a> -->
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No tasks for this month.</p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Navigation Buttons -->
                        <div class="navigation-buttons text-center my-4">
                            <?php if ($page === 'second-half'): ?>
                                <a href="calendar?page=first-half" class="btn btn-primary">Previous</a>
                            <?php endif; ?>
                            <?php if ($page === 'first-half'): ?>
                                <a href="calendar?page=second-half" class="btn btn-primary">Next</a>
                            <?php endif; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>       <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>
