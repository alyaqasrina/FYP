<?php
    session_start(); // Start the session to access session variables
    require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css" />
    <title>Calendify</title> 
    <nav class="navbar-homepage">
        <div class="logo">
            <img src="logo_new.png" alt="Calendify Logo" href="index.php">
            <h3 class="navbar-title">CALENDIFY</h3>
        </div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="tasks.php">Tasks</a>
            <a href="calendar.php">Calendar</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
        <input type="text" class="search-bar" placeholder="Search">
    </nav>
</head>
<body>

    <!-- Main Content -->
    <section class="homepage">
        <section class="header-section-homepage">
            <h2 class="index-title">Calendify: Smart Personal Assistant</h2>
            <p class="index-title-description">Organize your tasks efficiently</p>
        </section>

        <section class="welcome-section">
            <div class="welcome-message">
                <img src="avatar.png" class="avatar-image" alt="Avatar">
                <h3>
                    Welcome, 
                    <?php 
                    // Check if the username is set in the session
                    if (isset($_SESSION['username'])) {
                        echo htmlspecialchars($_SESSION['username']); // Use htmlspecialchars to prevent XSS
                    } else {
                        echo "Guest"; // Or handle it in another way if not logged in
                    }
                    ?>!
                </h3>
            </div>
        </section>

        <section class="task-list-section">
            <div class="task-wrapper">
                <div class="task-content-title">
                    <h2 class="task-title">Task List</h2>
                    <p class="task-title-description">Here are your tasks for today:</p>
                    <button onclick="window.location.href='add_task.php'" type="button" class="btn btn-primary">Add Task</button>
                </div>
                <div class="task-table">
                    <table>
                        <tr>
                            <th>Task Name</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                        <?php
                        // Fetch tasks from the database
                        $query = "SELECT * FROM tasks";
                        $result = mysqli_query($conn, $query);
                        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        // Display the tasks in the table
                        foreach ($tasks as $task) {
                            echo '<tr>';
                            echo '<td>' . $task['task_name'] . '</td>';
                            echo '<td>' . $task['due_date'] . '</td>';
                            echo '<td>' . $task['priority'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>
        </section>



    <!-- Footer -->
    <section class="footer-section">
        <footer class="footer">
        <div class="footer-container">
                <a href="homepage.php">Home</a>
                <a href="aboutus.php">About Us</a>
                <a href="privacypolicy.php">Privacy Policy</a>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Calendify. All rights reserved.
        </div>
        </footer>
    </section>

</body>
</html>



<div class="overflow-x-auto font-[sans-serif]">
      <table class="min-w-full bg-white">
        <thead class="whitespace-nowrap">
          <tr>
            <th class="p-4 text-sm font-semibold bg-blue-500 text-white text-center">
              Task List
            </th>
            <th class="p-4 text-sm font-semibold bg-red-500 text-white text-center">
              Assign
            </th>
            <th class="p-4 text-sm font-semibold bg-green-500 text-white text-center">
              Priority
            </th>
            <th class="p-4 text-sm font-semibold bg-yellow-500 text-white text-center">
              Status
            </th>
            <th class="p-4 text-sm font-semibold bg-purple-800 text-white text-center">
              Progress
            </th>
          </tr>
        </thead>

        <tbody class="whitespace-nowrap divide-y divide-gray-200">
          <tr class="hover:bg-gray-50">
            <td class="text-gray-800 text-center p-4 text-sm">
              Opening Meeting
            </td>
            <td class="text-gray-800 text-center p-4 text-sm">
              Joe
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-red-500 w-max text-white rounded">High</div>
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-green-500 w-max text-white rounded">Done</div>
            </td>
            <td class="text-center p-4 flex items-center">
              <div class="bg-gray-300 rounded-full w-full h-2 min-w-[50px]">
                <div class="w-4/5 h-full rounded-full bg-purple-800"></div>
              </div>
              <p class="text-xs text-gray-500 ml-2">80%</p>
            </td>
          </tr>

          <tr class="hover:bg-gray-50">
            <td class="text-gray-800 text-center p-4 text-sm">
              Planning
            </td>
            <td class="text-gray-800 text-center p-4 text-sm">
              Dan
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-yellow-500 w-max text-white rounded">Normal</div>
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-yellow-500 w-max text-white rounded">In Progress</div>
            </td>
            <td class="text-center p-4 flex items-center">
              <div class="bg-gray-300 rounded-full w-full h-2 min-w-[50px]">
                <div class="w-3/12 h-full rounded-full bg-purple-800"></div>
              </div>
              <p class="text-xs text-gray-500 ml-2">25%</p>
            </td>
          </tr>

          <tr class="hover:bg-gray-50">
            <td class="text-gray-800 text-center p-4 text-sm">
              Media engagement
            </td>
            <td class="text-gray-800 text-center p-4 text-sm">
              Josh
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-red-500 w-max text-white rounded">High</div>
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-yellow-500 w-max text-white rounded">In Progress</div>
            </td>
            <td class="text-center p-4 flex items-center">
              <div class="bg-gray-300 rounded-full w-full h-2 min-w-[50px]">
                <div class="w-2/5 h-full rounded-full bg-purple-800"></div>
              </div>
              <p class="text-xs text-gray-500 ml-2">40%</p>
            </td>
          </tr>

          <tr class="hover:bg-gray-50">
            <td class="text-gray-800 text-center p-4 text-sm">
              Development
            </td>
            <td class="text-gray-800 text-center p-4 text-sm">
              James
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-yellow-500 w-max text-white rounded">Normal</div>
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-yellow-500 w-max text-white rounded">In Progress</div>
            </td>
            <td class="text-center p-4 flex items-center">
              <div class="bg-gray-300 rounded-full w-full h-2 min-w-[50px]">
                <div class="w-11/12 h-full rounded-full bg-purple-800"></div>
              </div>
              <p class="text-xs text-gray-500 ml-2">91%</p>
            </td>
          </tr>

          <tr class="hover:bg-gray-50">
            <td class="text-gray-800 text-center p-4 text-sm">
              Testing
            </td>
            <td class="text-gray-800 text-center p-4 text-sm">
              John
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-green-500 w-max text-white rounded">Low</div>
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-green-500 w-max text-white rounded">Done</div>
            </td>
            <td class="text-center p-4 flex items-center">
              <div class="bg-gray-300 rounded-full w-full h-2 min-w-[50px]">
                <div class="w-1/5 h-full rounded-full bg-purple-800"></div>
              </div>
              <p class="text-xs text-gray-500 ml-2">20%</p>
            </td>
          </tr>

          <tr class="hover:bg-gray-50">
            <td class="text-gray-800 text-center p-4 text-sm">
              Project closure
            </td>
            <td class="text-gray-800 text-center p-4 text-sm">
              Nick
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-green-500 w-max text-white rounded">Low</div>
            </td>
            <td class="text-center p-4 text-xs">
              <div class="mx-auto px-3 py-1 bg-red-500 w-max text-white rounded">Cancel</div>
            </td>
            <td class="text-center p-4 flex items-center">
              <div class="bg-gray-300 rounded-full w-full h-2 min-w-[50px]">
                <div class="w-1/5 h-full rounded-full bg-purple-800"></div>
              </div>
              <p class="text-xs text-gray-500 ml-2">20%</p>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="md:flex m-4">
        <p class="text-sm text-gray-500 flex-1">Showind 1 to 5 of 100 entries</p>
        <div class="flex items-center max-md:mt-4">
          <p class="text-sm text-gray-500">Display</p>

          <select class="text-sm text-gray-500 border border-gray-400 rounded px-1 py-2 mx-4 outline-none">
            <option>5</option>
            <option>10</option>
            <option>20</option>
            <option>50</option>
            <option>100</option>
          </select>

          <div class="border flex rounded divide-x-2 border-gray-400 divide-gray-400 overflow-hidden">
            <button type="button" class="px-4 py-2 hover:bg-gray-100 text-sm">Previous</button>
            <button type="button" class="px-4 py-2 hover:bg-gray-100 text-sm">Next</button>
          </div>
        </div>
      </div>
    </div>