<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .task, .subtask {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Add Task</h1>
    <form action="add_task.php" method="POST">
        <div class="task">
            <h2>Main Task</h2>
            <label for="task_name">Task Name:</label>
            <input type="text" name="task_name" required><br>

            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" required><br>

            <label for="priority">Priority:</label>
            <select name="priority" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>

        <div class="subtask">
            <h2>Subtasks</h2>
            <div class="subtask-group">
                <label for="subtask_name">Subtask Name:</label>
                <input type="text" name="subtask_name[]" required><br>

                <label for="subtask_due_date">Subtask Due Date:</label>
                <input type="date" name="subtask_due_date[]" required><br>

                <label for="subtask_priority">Subtask Priority:</label>
                <select name="subtask_priority[]" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <button type="button" onclick="addSubtask()">Add Another Subtask</button>
        </div>

        <input type="submit" value="Save Task">
    </form>

    <script>
        function addSubtask() {
            const subtaskGroup = document.querySelector('.subtask-group');
            const newSubtask = subtaskGroup.cloneNode(true);
            newSubtask.querySelectorAll('input').forEach(input => input.value = '');
            document.querySelector('.subtask').appendChild(newSubtask);
        }
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get main task details
        $task_name = $_POST['task_name'];
        $due_date = $_POST['due_date'];
        $priority = $_POST['priority'];

        // Get subtasks details
        $subtask_names = $_POST['subtask_name'];
        $subtask_due_dates = $_POST['subtask_due_date'];
        $subtask_priorities = $_POST['subtask_priority'];

        // Display the main task
        echo "<h2>Task Added:</h2>";
        echo "<strong>Task Name:</strong> $task_name<br>";
        echo "<strong>Due Date:</strong> $due_date<br>";
        echo "<strong>Priority:</strong> $priority<br>";

        // Display subtasks
        echo "<h3>Subtasks:</h3>";
        for ($i = 0; $i < count($subtask_names); $i++) {
            echo "<strong>Subtask Name:</strong> " . htmlspecialchars($subtask_names[$i]) . "<br>";
            echo "<strong>Subtask Due Date:</strong> " . htmlspecialchars($subtask_due_dates[$i]) . "<br>";
            echo "<strong>Subtask Priority:</strong> " . htmlspecialchars($subtask_priorities[$i]) . "<br><br>";
        }
    }
    ?>
</body>
</html>