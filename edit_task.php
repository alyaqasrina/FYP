<?php
session_start();
include('db.php'); // Ensure this connects properly to the database

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in.");
}

$user_id = $_SESSION['user_id'];

// Retrieve task and subtasks data
if (isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']);

    // Fetch main task details
    $task_query = "SELECT * FROM `tasks` WHERE `task_id` = ? AND `user_id` = ?";
    $stmt = mysqli_prepare($conn, $task_query);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
    mysqli_stmt_execute($stmt);
    $task_result = mysqli_stmt_get_result($stmt);
    $task = mysqli_fetch_assoc($task_result);

    if (!$task) {
        die("Error: Task not found or you do not have access to it.");
    }
    // Fetch subtasks
    $subtask_query = "SELECT * FROM `subtasks` WHERE `task_id` = ?";
    $stmt = mysqli_prepare($conn, $subtask_query);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);
    $subtasks_result = mysqli_stmt_get_result($stmt);
    $subtasks = mysqli_fetch_all($subtasks_result, MYSQLI_ASSOC);
} else {
    die("Error: Task ID is required.");
}

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $due_time = mysqli_real_escape_string($conn, $_POST['due_time']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    // Update main task
    $update_task_query = "UPDATE `tasks` SET `task_name` = ?, `description` = ?, `due_date` = ?, `priority` = ? WHERE `task_id` = ? AND `user_id` = ?";
    $stmt = mysqli_prepare($conn, $update_task_query);

    $due_date_time = $due_date . ' ' . $due_time;

    mysqli_stmt_bind_param($stmt, "ssssii", $task_name, $task_description, $due_date_time, $priority, $task_id, $user_id);

    if (!mysqli_stmt_execute($stmt)) {
        die("Error updating task: " . mysqli_error($conn));
    }

    // Handle subtasks updates
    $subtask_ids = $_POST['subtask_id'] ?? [];
    $subtask_names = $_POST['subtask_name'] ?? [];
    $subtask_due_dates = $_POST['subtask_due_date'] ?? [];
    $subtask_due_times = $_POST['subtask_due_time'] ?? [];
    $subtask_priorities = $_POST['subtask_priority'] ?? [];

    foreach ($subtask_ids as $index => $subtask_id) {
        $subtask_name = mysqli_real_escape_string($conn, $subtask_names[$index]);
        $subtask_due_date = mysqli_real_escape_string($conn, $subtask_due_dates[$index]);
        $subtask_due_time = mysqli_real_escape_string($conn, $subtask_due_times[$index]);
        $subtask_priority = mysqli_real_escape_string($conn, $subtask_priorities[$index]);

        if (empty($subtask_name) || empty($subtask_due_date) || empty($subtask_priority) || empty($subtask_due_time)) {
            die("Error: All subtask fields are required.");
        }

        // Update or insert subtasks
        if ($subtask_id) {
            $update_subtask_query = "UPDATE `subtasks` SET `subtask_name` = ?, `subtask_due_date` = ?, `subtask_priority` = ? WHERE `subtask_id` = ? AND `task_id` = ?";
            $stmt = mysqli_prepare($conn, $update_subtask_query);

            $subtask_due_date_time = $subtask_due_date . ' ' . $subtask_due_time;

            mysqli_stmt_bind_param($stmt, "sssii", $subtask_name, $subtask_due_date_time, $subtask_priority, $subtask_id, $task_id);
            mysqli_stmt_execute($stmt);
        } else {
            $insert_subtask_query = "INSERT INTO `subtasks` (`task_id`, `subtask_name`, `subtask_due_date`, `subtask_priority`) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_subtask_query);

            $subtask_due_date_time = $subtask_due_date . ' ' . $subtask_due_time;

            mysqli_stmt_bind_param($stmt, "isss", $task_id, $subtask_name, $subtask_due_date_time, $subtask_priority);
            mysqli_stmt_execute($stmt);
        }
    }
    // JavaScript for Success Popup and Redirect
    echo "<script>
    window.location.href = 'task';
    </script>";
    exit;
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
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [v-cloak] {
            display: none;
        }
    </style>
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
            <main>
                <form action="edit_task.php?task_id=<?= $task_id ?>" method="POST">
                    <div id="app" v-cloak>
                        <div class="container mx-auto p-4">
                            <!-- Main Task Form -->
                            <div class="bg-white rounded-lg shadow p-6 mb-6">
                                <h2 class="text-2xl font-bold mb-4">Edit Main Task</h2>
                                <p class="text-gray-600 mb-4">Edit the details of the main task</p>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Task Name:</label>
                                        <input
                                            v-model="mainTask.name"
                                            type="text"
                                            name="task_name"
                                            class="w-full p-2 border rounded">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Task Description:</label>
                                        <input
                                            v-model="mainTask.description"
                                            type="text"
                                            name="description"
                                            class="w-full p-2 border rounded">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Due Date:</label>
                                        <input
                                            v-model="mainTask.dueDate"
                                            type="date"
                                            name="due_date"
                                            class="w-full p-2 border rounded">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Due Time:</label>
                                        <input
                                            v-model="mainTask.dueTime"
                                            type="time"
                                            name="due_time"
                                            class="w-full p-2 border rounded">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Priority:</label>
                                        <select
                                            v-model="mainTask.priority"
                                            name="priority"
                                            class="w-full p-2 border rounded">
                                            <option value="High">High</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Low">Low</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Task Status:</label>
                                        <select
                                            v-model="mainTask.status"
                                            name="status"
                                            class="w-full p-2 border rounded">
                                            <option value="Pending">Pending</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Subtasks Section -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-2xl font-bold mb-4">Edit Subtasks</h2>
                                <p class="text-gray-600 mb-4">Edit the details of the subtasks</p>

                                <!-- Existing Subtasks -->
                                <div v-for="(subtask, index) in subtasks" :key="index" class="mb-6 p-4 border rounded">
                                    <h3 class="font-bold mb-4">Subtask {{ index + 1 }}</h3>

                                    <div class="space-y-4">

                                        <input
                                            v-model="subtask.id"
                                            type="hidden"
                                            name="subtask_id[]">

                                        <div>
                                            <label class="block text-sm font-medium mb-1">Subtask Name:</label>
                                            <input
                                                v-model="subtask.name"
                                                name="subtask_name[]"
                                                type="text"
                                                class="w-full p-2 border rounded">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-1">Due Date:</label>
                                            <input
                                                v-model="subtask.dueDate"
                                                type="date"
                                                name="subtask_due_date[]"
                                                class="w-full p-2 border rounded">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-1">Due Time:</label>
                                            <input
                                                v-model="subtask.dueTime"
                                                type="time"
                                                name="subtask_due_time[]"
                                                class="w-full p-2 border rounded">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-1">Priority:</label>
                                            <select
                                                v-model="subtask.priority"
                                                name="subtask_priority[]"
                                                class="w-full p-2 border rounded">
                                                <option value="High">High</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Low">Low</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-1">Status:</label>
                                            <select
                                                v-model="subtask.status"
                                                name="subtask_status[]"
                                                class="w-full p-2 border rounded">
                                                <option value="Not Started">Not Started</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>

                                        <button
                                            @click="removeSubtask(index)"
                                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                            Remove Subtask
                                        </button>
                                    </div>
                                </div>

                                <!-- Add Subtask and Save Changes Buttons -->
                                <div class="flex gap-4">
                                    <button
                                        @click="addSubtask"
                                        type="button"
                                        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                                        Add Subtask
                                    </button>

                                    <button
                                        type="submit"
                                        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

                <script type='text/javascript'>
                    <?php
                    $subtasks_mapped = array_map(function ($subtask) {
                        return [
                            'id' => $subtask['subtask_id'],
                            'name' => $subtask['subtask_name'],
                            'dueDate' => explode(' ', $subtask['subtask_due_date'])[0] ?: '',
                            'dueTime' => isset(explode(' ', $subtask['subtask_due_date'])[1]) ? explode(' ', $subtask['subtask_due_date'])[1] : '',
                            'priority' => $subtask['subtask_priority'],
                            'status' => $subtask['subtask_status']
                        ];
                    }, $subtasks);
                    $subtasks_json = json_encode($subtasks_mapped);
                    echo "const subtasksJSON = " . $subtasks_json . ";\n";
                    ?>
                </script>

                <script type="module">
                    import {
                        createApp,
                        ref
                    } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'

                    createApp({
                        name: 'TaskManagementForm',

                        data() {

                            return {
                                mainTask: {
                                    name: "<?= htmlspecialchars($task['task_name']) ?>",
                                    description: "<?= htmlspecialchars($task['description']) ?>",
                                    dueDate: "<?= htmlspecialchars(explode(' ', $task['due_date'])[0] ?: '') ?>",
                                    dueTime: "<?= htmlspecialchars(isset(explode(' ', $task['due_date'])[1]) ? explode(' ', $task['due_date'])[1] : '') ?>",
                                    priority: "<?= htmlspecialchars($task['priority']) ?>",
                                    status: "<?= htmlspecialchars($task['status']) ?>",
                                },
                                subtasks: subtasksJSON,
                            }
                        },

                        methods: {
                            addSubtask() {
                                this.subtasks.push({
                                    id: '',
                                    name: '',
                                    dueDate: '',
                                    dueTime: '',
                                    priority: 'Medium',
                                    status: 'Not Started'
                                })
                            },

                            removeSubtask(index) {
                                this.subtasks.splice(index, 1)
                            },

                        }
                    }).mount('#app')
                </script>

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
                        <select name="subtask_priority[]" class="form-control" required>                                <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                        <label for="subtask_priority">Priority:</label>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeSubtask(this)">Remove Subtask</button>
                    <button type="button" class="btn btn-primary" onclick="addSubtask()">Add Another Subtask</button>
                    <button type="submit" class="btn btn-primary">Save Task</button>
                    `;

            // Append the new subtask form to the container
            subtaskForm.appendChild(newSubtask);

            // Update button visibility
            toggleButtons();
        }

        function removeSubtask(button) {
            const subtaskGroup = button.parentElement; // Get the parent `.subtask-group` div
            subtaskGroup.remove();

            // Update button visibility
            toggleButtons();
        }

        function toggleButtons() {
            const subtaskGroups = document.querySelectorAll('.subtask-group'); // Get all subtask groups
            const mainButtons = document.querySelector('.main-buttons'); // Main buttons container

            if (subtaskGroups.length === 0) {
                // Show the main buttons if no subtasks exist
                mainButtons.style.display = 'block';
            } else {
                // Hide the main buttons if subtasks exist
                mainButtons.style.display = 'none';

                // Ensure only the last subtask group displays its buttons
                subtaskGroups.forEach((group, index) => {
                    const addButton = group.querySelector('.btn-primary'); // Add Another Subtask button
                    const saveButton = group.querySelector('.btn-primary'); // Save Task button

                    if (index === subtaskGroups.length - 1) {
                        // Show buttons for the last subtask group
                        addButton.style.display = 'inline-block';
                        saveButton.style.display = 'inline-block';
                    } else {
                        // Hide buttons for all other subtask groups
                        addButton.style.display = 'none';
                        saveButton.style.display = 'none';
                    }
                });
            }

            // Special case: Ensure first subtask always has buttons if it is the only one
            if (subtaskGroups.length === 1) {
                const firstGroup = subtaskGroups[0];
                const addButton = firstGroup.querySelector('.btn-primary');
                const saveButton = firstGroup.querySelector('.btn-primary');
                addButton.style.display = 'inline-block';
                saveButton.style.display = 'inline-block';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</body>

</html>