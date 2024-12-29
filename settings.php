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

$query = "SELECT notification_method FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($notification_method);
$stmt->fetch();
$notification_method = isset($_POST['notification_method']) ? $_POST['notification_method'] : '';

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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybP0bVZpNURyKa7B3rumvq5N+8eA2k1hSewzMFL5OI86LShqs" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuS3IfkITSMOHVCj8Jb41PVjFMXISsG2RZRZNRBLBkaSmgBOnIIL5T8ZlW9ORyxN" crossorigin="anonymous"></script>

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
                    <li><hr class="dropdown-divider" /></li>
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
                        <div class="sb-sidenav-menu-heading"><Main></Main></div>
                        <a class="nav-link" href="homepage.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Settings</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Overview
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="settings.php">Profile & Notifications</a>
                                <a class="nav-link" href="reminders.php">Send Reminder</a>
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
                    <h1 class="mt-4">Settings</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item-active">Adjust your profile and notification settings how you want it!</li>
                    </ol>
                    <div class="row">
                    <div class="col-xl-3 col-md-6"></div>
                    <form action="save_settings.php" method="POST">
                        <div class="settings-container">
                            <!-- Profile Settings -->
                            <div class="main-task-form">
                            <h2 class="main-task-title">Profile Settings</h2>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="inputUsername" placeholder="Enter your new userame" name="username" value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>">
                                    <label for="inputUsername">Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Enter your new email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>">
                                    <label for="inputEmail">Email</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="inputPhoneNumber" placeholder="Enter your new phone number" name="phone_number" value="<?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number']) : ''; ?>">
                                    <label for="inputPhoneNumber">Phone Number</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="inputPassword" placeholder="Enter your new password" name="password">
                                    <label for="inputPassword">Password</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="inputConfirmPassword" placeholder="Confirm your new password" name="confirm_password">
                                    <label for="inputConfirmPassword">Confirm Password</label>
                                </div>
                                <button class="btn btn-primary" type="submit">Save Settings</button>
                            </div>
                            <!-- Notification Settings -->
                             <div class="subtask-form">
                                <h2 class="subtask-title">Notification Preferences</h2>
                                <div class="form-floating mb-3">
                                    <select name="notification_method" class="form-control">
                                        <option value="email" <?php if ($notification_method == 'email') echo 'selected'; ?>>Email</option>
                                        <option value="sms" <?php if ($notification_method == 'sms') echo 'selected'; ?>>SMS</option>
                                        <option value="inapp" <?php if ($notification_method == 'inapp') echo 'selected'; ?>>In-App</option>
                                    </select>
                                    <label for="notification_method">Notification Method</label>
                                </div>
                                <button class="btn btn-primary" type="submit">Save Settings</button>
                             </div>
                        </div>
                    </form>
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
<!-- Success Popup -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="  position: fixed;
                top: 20px; /* Adjust to set distance from top */
                left: 50%;
                transform: translateX(-50%);
                z-index: 1050; /* Ensures it appears above other content */
                width: 50%; /* Adjust width as needed */
                text-align: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        Settings Saved Successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
