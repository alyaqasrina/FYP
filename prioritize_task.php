<?php
include('db.php');

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

function mapPriorityLevel($priorityLevel) {
    $priorityMap = [
        'high' => 3,
        'medium' => 2,
        'low' => 1
    ];
    return $priorityMap[strtolower($priorityLevel)] ?? 0; // Default to 0 if invalid
}

function calculatePriority($dueTime, $priorityLevel, $complexity) {
    $priorityScore = mapPriorityLevel($priorityLevel) * 3; // Triple weight for high priority
    $timeScore = max(0, strtotime($dueTime) - time()) / 3600; // Normalize by hours
    $complexityScore = 5 - $complexity; // Simpler tasks score higher

    return $priorityScore + $complexityScore - $timeScore;
}

function calculateProgress()

// Fetch tasks from the database
$query = "SELECT * FROM tasks WHERE due_date = CURDATE() AND priority IN ('high', 'medium', 'low')";
$result = mysqli_query($conn, $query);

$tasks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $priority = calculatePriority($row['due_date'], $row['priority'], $row['complexity']);
    $tasks[] = ['task' => $row, 'priority' => $priority];
}

usort($tasks, function($a, $b) {
    return $b['priority'] <=> $a['priority']; // Sort in descending order of priority
});

// Get the highest-priority task
$topTask = $tasks[3]['task'];

?>