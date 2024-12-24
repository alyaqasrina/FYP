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
                            Settings
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="send_reminder.php">Send Reminder</a>
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

        <div class="setting-container">
            <div class="row justify-content-center">
                <!-- Profile Settings -->
                <div class="col-lg-6">
                    <div class="card-setting">
                        <div class="card-header-setting">Profile Information</div>
                        <div class="card-body-setting">
                            <form action="save_settings.php" method="POST">
                                <label for="inputUsername">Name</label>
                                <input
                                    class="form-control"
                                    id="inputUsername"
                                    value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>"
                                    name="username"
                                    type="text"
                                    placeholder="Enter your name"
                                    required
                                />
                                <label for="inputEmail">Email</label>
                                <input
                                    class="form-control"
                                    id="inputEmail"
                                    value="<?php echo $user['email']; ?>"
                                    name="email"
                                    type="email"
                                    placeholder="Enter your email"
                                    required
                                />
                                <label for="inputPassword">Password</label>
                                <input
                                    class="form-control"
                                    id="inputPassword"
                                    name="password"
                                    type="password"
                                    placeholder="Enter new password"
                                    required
                                />
                                <button class="btn mt-3" type="submit">Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="col-lg-6">
                    <div class="card-setting">
                        <div class="card-header-setting">Notification Preferences</div>
                        <div class="card-body-setting">
                            <form action="save_settings.php" method="POST">
                                <label for="notification_method">Email Notifications</label>
                                <select name="email_notifications" class="form-control-setting">
                                    <option value="on">On</option>
                                    <option value="off">Off</option>
                                </select>
                                <label for="sms_notifications">SMS Notifications</label>
                                <select name="sms_notifications" class="form-control-setting">
                                    <option value="on">On</option>
                                    <option value="off">Off</option>
                                </select>
                                <label for="inapp_notifications">In-App Notifications</label>
                                <select name="inapp_notifications" class="form-control-setting">
                                    <option value="on">On</option>
                                    <option value="off">Off</option>
                                </select>
                                <button class="btn mt-3" type="submit">Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>
    </div>
</div>
</body>
</html>

   <!-- Popup for 'Settings Saved Successfully' -->
   <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="popup-success">
                Settings saved successfully!
            </div>
    <?php endif; ?>