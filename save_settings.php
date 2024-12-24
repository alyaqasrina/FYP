<?php
session_start();
include('db.php');

// Get form data
$name = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$notification_method = $_POST['notification_method'];
$user_id = $_SESSION['user_id']; // Assuming user is logged in

// Update query
$query = "UPDATE users SET name='$name', email='$email', phone='$phone', notification_method='$notification_method' WHERE id='$user_id'";

if (mysqli_query($conn, $query)) {
    // Redirect back with success message
    header("Location: settings.php?status=success");
    exit();
} else {
    echo "Error updating settings: " . mysqli_error($conn);
}
?>
