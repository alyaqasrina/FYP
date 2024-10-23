<?php
   session_start(); // Start the session to access the user ID

   // Check if the user is logged in
   if (!isset($_SESSION['user_id'])) {
       header('Location: login.php'); // Redirect to login page if not logged in
       exit();
   }
   
   $user_id = $_SESSION['user_id']; // Get the user ID from the session
   
   // Check if the user ID exists in the users table
   $userCheckSql = "SELECT user_id FROM users WHERE user_id = '$user_id'";
   $userCheckResult = $conn->query($userCheckSql);
   if ($userCheckResult->num_rows == 0) {
       die("Invalid user ID.");
   }
   ?>