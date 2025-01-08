<?php
// Database connection
include('db.php');

// Get user ID from session (assuming you have a login system)
session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['profile_picture'])) {

        $user_id = intval($_POST['user_id']);
        $file = $_FILES['profile_picture'];

        // Validate the file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            die("Only JPEG, PNG, and GIF files are allowed.");
        }

        if ($file['size'] > 2 * 1024 * 1024) { // Limit to 2MB
            die("File size must be less than 2MB.");
        }

        // Generate a unique file name
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_name = uniqid('profile_', true) . '.' . $extension;

        // Set the target directory and file path
        $target_dir = "profile_photos/";
        $target_file = $target_dir . $unique_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update the user's profile picture in the database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("si", $target_file, $user_id);

            $stmt->close();
        } else {
            echo "Failed to upload file.";
        }
    }

    $username = $conn->real_escape_string($_POST['username']);

    $update_sql = "UPDATE users SET 
                   username = '$username'
                   WHERE user_id = $user_id";

    if ($conn->query($update_sql)) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}

// Get user data
$sql = "SELECT username, email, phone_number, profile_picture
        FROM users 
        WHERE user_id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
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
                        <div class="sb-sidenav-menu-heading">
                            <Main></Main>
                        </div>
                        <a class="nav-link" href="homepage.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Task</div>
                        <a class="nav-link" aria-expanded="true" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Overview
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse show" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
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
                <div class="container py-5">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h1 class="card-title h3 mb-4">Edit Profile</h1>

                                    <?php if (isset($success_message)): ?>
                                        <div class="alert alert-success" role="alert">
                                            <?php echo $success_message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $error_message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="">

                                        <div class="mb-3 text-center" onclick="onProfilePictureEdit()">
                                            <label for="profile_picture" class="form-label d-block">Profile Picture</label>
                                            <input type="file" name="profile_picture" style="display: none;" id="profile_picture" class="d-none">
                                            <img src="https://i.guim.co.uk/img/media/5d3da3fc2c5de789cd7bff885ddfbcf15d729209/0_244_4080_2448/master/4080.jpg?width=1200&height=900&quality=85&auto=format&fit=crop&s=68356c53e1e1b1d6c737443c6b005709"
                                                alt="Profile Picture"
                                                id="profile_picture_preview"
                                                class="rounded-circle"
                                                style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                                onclick="document.getElementById('profile_picture').click();">
                                        </div>

                                        <div class="mb-3">
                                            <label for="username" class="form-label">User Name</label>
                                            <input type="text" class="form-control" name="username" id="username"
                                                value="<?php echo htmlspecialchars($user['username']); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                value="<?php echo htmlspecialchars($user['email']); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" name="phone_number" id="phone_number"
                                                value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                                    </form>
                                </div>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script src="js/profile-picture-edit.js"></script>

</body>

</html>