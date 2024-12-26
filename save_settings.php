<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get form data
$name = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
$notification_method = mysqli_real_escape_string($conn, $_POST['notification_method']);
$user_id = $_SESSION['user_id'];

// Update query
$query = "UPDATE users SET username=?, email=?, phone_number=?, notification_method=? WHERE user_id=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $notification_method, $user_id);

if (mysqli_stmt_execute($stmt)) {
    // Redirect back to settings page with success message
    header("Location: settings.php?status=success");
    exit();
} else {
    // Handle errors
    echo "Error updating settings: " . mysqli_error($conn);
    exit();
}
?>
