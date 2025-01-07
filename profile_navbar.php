<?php 

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

?>

<ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo $user['profile_picture']; ?>" alt="" style="height: 40px; width: 40px; border-radius: 50%;">
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li>
                <h5 class="dropdown-header" style="font-weight: bold; color:black">
                    <?php
                    echo $user['username'];
                    ?>
                </h5>
                <h5 class="dropdown-header">
                    <?php
                    echo $user['email'];
                    ?>
                </h5>
            </li>

            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item" href="settings.php">Setting</a></li>
            <li>
                <hr class="dropdown-divider" />
            </li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
    </li>
</ul>