<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch reminders for the logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM reminders WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reminders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Check for success or error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Clear messages after displaying them
unset($_SESSION['success'], $_SESSION['error']);

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
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Settings
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="settings.php">Profile & Notifications</a>
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

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Reminder Settings</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item-active">Manage your reminders</li>
                    </ol>


                    <!-- Display success or error messages -->
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- Display reminders -->
                    <div class="row">
                    <div class="col-xl-3 col-md-6"></div>
                    <form action="save_reminder.php" method="POST">
                        <div class="reminder-container">
                            <div class="reminder-form">
                                <!-- Reminder Frequency -->
                                <div class="mb-3 form-group">
                                    <input type="text" class="form-control" id="reminder_name" name="reminder_name" required>
                                    <label for="reminder_name" class="form-label">Reminder Text</label>
                                </div>
                                <div class="mb-3 form-group">
                                    <select class="form-select" id="frequency" name="frequency" required>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                    <label for="frequency" class="form-label">Reminder Frequency</label>
                                </div>

                                <div class="mb-3 form-group">
                                    <input type="time" class="form-control" id="time" name="time" required>
                                    <label for="time" class="form-label">Reminder Time</label>
                                </div>

                                <div class="mb-3 form-group" id="custom-fields">
                                    <label class="form-label mb-2" style="font-weight: 500;">Custom Days (Select days)</label><br>
                                    <div class="d-flex flex-wrap gap-3"></div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Monday">
                                        <label class="form-check-label">Monday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Tuesday">
                                        <label class="form-check-label">Tuesday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Wednesday">
                                        <label class="form-check-label">Wednesday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Thursday">
                                        <label class="form-check-label">Thursday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Friday">
                                        <label class="form-check-label">Friday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Saturday">
                                        <label class="form-check-label">Saturday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Sunday">
                                        <label class="form-check-label">Sunday</label>
                                    </div>
                                </div>

                                <!-- Custom Date (Optional) -->
                                <div class="mb-3 form-group">
                                    <input type="date" class="form-control" id="custom_date" name="custom_date">
                                    <label for="custom_date" class="form-label">Custom Date (Optional)</label>
                                </div>

                                <!-- Reminder Method -->
                                <div class="mb-3 form-group">
                                    <select class="form-select " id="notification_method" name="notification_method" required>
                                        <option value="email">Email</option>
                                        <option value="WhatsApp">Whatsapp</option>
                                        <option value="in-app">In-app</option>
                                    </select>
                                    <label for="notification_method" class="form-label">Notification Method</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-30">Save Reminder</button>
                            </div>
                        </div>
                    </form>
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
     <!-- JavaScript to Show/Hide Custom Fields -->
     <script>
        document.getElementById('frequency').addEventListener('change', function() {
            var customFields = document.getElementById('custom-fields');
            if (this.value === 'custom') {
                customFields.style.display = 'block';
            } else {
                customFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
































