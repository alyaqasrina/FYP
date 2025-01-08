<?php
session_start();
include('db.php'); // Ensure this connects properly to the database

// Function to determine priority class
function getPriorityClass($priority) {
    switch (strtolower($priority)) {
        case 'low':
            return 'low-priority';
        case 'medium':
            return 'medium-priority';
        case 'high':
            return 'high-priority';
        default:
            return '';
    }
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Error: User is not logged in.");
    }

    // Validate and sanitize inputs
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $due_time = mysqli_real_escape_string($conn, $_POST['due_time']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $complexity = mysqli_real_escape_string($conn, $_POST['complexity']);

    // Insert main task into the database
    $query = "INSERT INTO `tasks` (task_name, description, due_date, priority, complexity, user_id) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    $concat_due_date = $due_date . ' ' . $due_time;

    mysqli_stmt_bind_param($stmt, "sssssi", $task_name, $task_description, $concat_due_date, $priority, $complexity, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $task_id = mysqli_insert_id($conn); // Get the inserted task's ID

        if (!empty($_POST['subtask_name']) && !empty($_POST['subtask_due_date']) && !empty($_POST['subtask_priority'])) {
            // Filter out empty entries in subtask arrays
            $subtask_names = array_filter($_POST['subtask_name']);
            $subtask_due_dates = array_filter($_POST['subtask_due_date']);
            $subtask_due_times = array_filter($_POST['subtask_due_time']);
            $subtask_priorities = array_filter($_POST['subtask_priority']);
        
            // Ensure all arrays have the same number of elements
            if (count($subtask_names) !== count($subtask_due_dates) || count($subtask_due_dates) !== count($subtask_priorities)) {
                die("Error: Mismatch in the number of subtask fields provided.");
            }
        
            $subtask_query = "INSERT INTO `subtasks` (task_id, subtask_name, subtask_due_date, subtask_priority) VALUES (?, ?, ?, ?)";
            $subtask_stmt = mysqli_prepare($conn, $subtask_query);
        
            foreach ($subtask_names as $index => $subtask_name) {
                if (empty($subtask_names[$index]) || empty($subtask_due_dates[$index]) || empty($subtask_priorities[$index]) || empty($subtask_due_times[$index])) {
                    die("Error: All subtask fields are required.");
                }
        
                $subtask_name = mysqli_real_escape_string($conn, $subtask_name);
                $subtask_due_date = mysqli_real_escape_string($conn, $subtask_due_dates[$index]);
                $subtask_due_time = mysqli_real_escape_string($conn, $subtask_due_times[$index]);
                $subtask_priority = mysqli_real_escape_string($conn, $subtask_priorities[$index]);

                $subtask_due_date = $subtask_due_date . ' ' . $subtask_due_time;
        
                mysqli_stmt_bind_param($subtask_stmt, "isss", $task_id, $subtask_name, $subtask_due_date, $subtask_priority);
                if (!mysqli_stmt_execute($subtask_stmt)) {
                    die("Error inserting subtask: " . mysqli_stmt_error($subtask_stmt));
                }
            }
            mysqli_stmt_close($subtask_stmt);
        } else {
            die("Error: Subtasks not properly submitted.");
        }        
    }
        
        // Display the main task and subtasks
        echo "<h2>Task Added:</h2>";
        echo "<table>";
        echo "<tr><th>Task Name</th><th>Task Description</th><th>Due Date</th><th>Priority</th></tr>";
        echo "<tr><td>$task_name</td><td>$task_description</td><td>$due_date</td><td class='task-priority " . getPriorityClass($priority) . "'>$priority</td></tr>";
        echo "</table>";

        echo "<h3>Subtasks:</h3>";
        echo "<table>";
        echo "<tr><th>Subtask Name</th><th>Subtask Due Date</th><th>Subtask Priority</th></tr>";
        for ($i = 0; $i < count($subtask_names); $i++) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($subtask_names[$i]) . "</td>";
            echo "<td>" . htmlspecialchars($subtask_due_dates[$i]) . "</td>";
            echo "<td class='task-priority " . getPriorityClass($subtask_priority[$i]) . "'>" . htmlspecialchars($subtask_priority[$i]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Task added successfully!</p>";
        echo "<button onclick='window.location.href=\"homepage.php\"' class='btn btn-primary'>Go Back</button>";
    } else {
        echo "<p>Error adding task: " . mysqli_error($conn) . "</p>";
    }

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
        <?php include('profile_navbar.php'); ?>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading"><Main></Main></div>
                        <a class="nav-link" href="homepage">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Task</div>
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Overview
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse show" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="task">Task</a>
                                <a class="nav-link" href="monitor_status">Monitor Status</a>
                                <a class="nav-link" href="calendar">Calendar</a>
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
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Add Task</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Please fill out the details of your task and subtasks</li>
                    </ol>
                    <div class="row">
                    <div class="col-xl-3 col-md-6"></div>
                    <form action="add_task" method="POST">
                        <div class="add-task-container">
                            <div class="main-task-form">
                                <h2 class="main-task-title">Main Task</h2>
                                <p class="main-task-description">Enter the details of the main task</p>
                                
                                <div class="form-floating mb-3"> 
                                    <input type="text" name="task_name"class="form-control" id="inputTaskName" name="taskName" required>
                                    <label for="task_name" >Task Name:</label>
                                </div>

                                <div class="form-floating mb-3"> 
                                    <input type="text" name="description" class="form-control" id="inputTaskDesc" name="taskDesc" required>
                                    <label for="description">Task Description:</label>
                                </div>

                                <div class="form-floating mb-3"> 
                                    <input type="date" name="due_date" class="form-control" id="inputDueDate" name="dueDate" required>
                                    <label for="due_date">Due Date:</label>
                                </div>

                                <div class="form-floating mb-3"> 
                                    <input type="time" name="due_time" class="form-control" required>
                                    <label for="due_time">
                                        Due Time:
                                    </label>
                                </div>

                                <div class="form-floating mb-3"> 
                                    <select name="priority" class="form-control" id="inputPriority" name="priority" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                    <label for="priority" class="task-priority">Priority:</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="complexity" name="complexity" class="form-control" id="inputComplexity" name="complexity" required>
                                    <label for="complexity">Complexity Level (1-5):</label>
                                </div>
                            </div>
                            
                            <div class="subtask-form">
                            <h2 class="subtask-title">Subtasks</h2>
                            <p class="subtask-description">Enter the details of the subtasks</p>

                            <!-- Existing Subtask Group -->
                            <div class="subtask-group">
                                <div class="form-floating mb-3"> 
                                    <input type="text" name="subtask_name[]" class="form-control" required>
                                    <label for="subtask_name">Subtask Name:</label>
                                </div>
                                <div class="form-floating mb-3"> 
                                    <input type="date" name="subtask_due_date[]" class="form-control" required>
                                    <label for="subtask_due_date">Due Date:</label>
                                </div>
                                <div class="form-floating mb-3"> 
                                    <input type="time" name="subtask_due_time[]" class="form-control" required>
                                    <label for="subtask_due_time">
                                        Due Time:
                                    </label>
                                </div>
                                <div class="form-floating mb-3"> 
                                    <select name="subtask_priority[]" class="form-control" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                    <label for="subtask_priority">Priority:</label>
                                </div>

                            </div>
                            <!-- Main Buttons (Visible initially) -->
                            <div class="main-buttons">
                                <button type="button" class="btn btn-warning" onclick="addSubtask()">Add Another Subtask</button>
                                <button type="submit" class="btn btn-primary">Save Task</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
        </div>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted"> &copy; 2024 Calendify. All rights reserved</div>
                        <div>
                            <a href="Privacy_policy.php">Privacy Policy</a>
                                &middot;
                            <a href="Terms_conditions.php">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <script>
    function addSubtask() {
        const subtaskForm = document.querySelector('.subtask-form'); // The container for subtasks

        // Create a new subtask HTML structure dynamically
        const newSubtask = document.createElement('div');
        newSubtask.classList.add('subtask-group', 'mb-3'); // Add a class for styling if needed

        // Add input fields for the new subtask
        newSubtask.innerHTML = `
            <div class="form-floating mb-3"> 
                <input type="text" name="subtask_name[]" class="form-control" required>
                <label for="subtask_name">Subtask Name:</label>
            </div>
            <div class="form-floating mb-3"> 
                <input type="date" name="subtask_due_date[]" class="form-control" required>
                <label for="subtask_due_date">Due Date:</label>
            </div>
            <div class="form-floating mb-3"> 
                <select name="subtask_priority[]" class="form-control" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
                <label for="subtask_priority">Priority:</label>
            </div>
            <button type="button" class="btn btn-danger" onclick="removeSubtask(this)">Remove Subtask</button>
            <button type="button" class="btn btn-warning add-subtask-btn" onclick="addSubtask()">Add Another Subtask</button>
            <button type="submit" class="btn btn-primary save-task-btn">Save Task</button>
        `;

        // Append the new subtask form to the container
        subtaskForm.appendChild(newSubtask);

        // Update button visibility
        updateButtonVisibility();
    }

    function removeSubtask(button) {
        const subtaskGroup = button.parentElement; // Get the parent `.subtask-group` div
        subtaskGroup.remove();

        // Update button visibility
        updateButtonVisibility();
    }

    function updateButtonVisibility() {
        const subtaskGroups = document.querySelectorAll('.subtask-group'); // Get all subtask groups

        subtaskGroups.forEach((group, index) => {
            const removeButton = group.querySelector('.btn-danger');
            const addButton = group.querySelector('.add-subtask-btn'); // "Add Another Subtask" button
            const saveButton = group.querySelector('.save-task-btn'); // "Save Task" button

            // Ensure only the last group shows its buttons
            if (index === subtaskGroups.length - 1) {
                if (removeButton) removeButton.style.display = 'inline-block';
                if (addButton) addButton.style.display = 'inline-block';
                if (saveButton) saveButton.style.display = 'inline-block';
            } else {
                if (removeButton) removeButton.style.display = 'none';
                if (addButton) addButton.style.display = 'none';
                if (saveButton) saveButton.style.display = 'none';
            }
        });

        // Handle the first subtask group explicitly
        if (subtaskGroups.length === 1) {
            const firstGroup = subtaskGroups[0];
            const removeButton = firstGroup.querySelector('.btn-danger');
            const addButton = firstGroup.querySelector('.add-subtask-btn');
            const saveButton = firstGroup.querySelector('.save-task-btn');

            // Ensure buttons are visible if it's the only group
            if (removeButton) removeButton.style.display = 'inline-block';
            if (addButton) addButton.style.display = 'inline-block';
            if (saveButton) saveButton.style.display = 'inline-block';
        }
    }

    // Ensure proper button visibility after page load (in case subtasks exist already)
    document.addEventListener('DOMContentLoaded', updateButtonVisibility);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>       <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>