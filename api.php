<?php
session_start();

// Database connection
$connection = mysqli_connect("localhost", "root", "", "fyp");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch prioritized tasks
function fetchPrioritizedTasks($connection, $user_id) {
    $query = "
        SELECT * 
        FROM tasks 
        WHERE user_id = $user_id 
        ORDER BY 
            FIELD(priority_level, 'High', 'Medium', 'Low'), 
            due_time ASC, 
            estimated_time_to_complete ASC
    ";

    $result = mysqli_query($connection, $query);

    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }

    return $tasks;
}

// Fetch tasks and encode as JSON
$tasks = fetchPrioritizedTasks($connection, $user_id);
header('Content-Type: application/json');
echo json_encode($tasks);
?>
