<?php

require('auth_session.php');
require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Page - Calendify</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
</head>
<body>
    <header>
        <h1>Calendar Overview</h1>
        <a href="homepage.php">Back to Homepage</a>
    </header>

    <div id="calendar"></div>

    <!-- Set Up Calendar Container and Include jQuery -->
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                events: [
                    // This is where you load tasks from the database to show on the calendar
                    <?php
                    $userId = $_SESSION['user_id']; // Assuming a session stores logged-in user data
                    $query = "SELECT * FROM tasks WHERE user_id = '$userId'";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "{
                            title: '{$row['title']}',
                            start: '{$row['due_date']}',
                            priority: '{$row['priority']}',
                            description: '{$row['description']}',
                            task_id: '{$row['id']}'
                        },";
                    }
                    ?>
                ],
                eventClick: function(event, jsEvent, view) {
                    if (event.task_id) {
                        let url = "edit_task.php?task_id=" + event.task_id;
                        window.location.href = url; // Redirect to task editing page
                    }
                },
                selectable: true,
                selectHelper: true,
                select: function(start, end) {
                    let title = prompt("Enter Task Title");
                    let priority = prompt("Enter Priority (Low, Medium, High)");
                    let description = prompt("Enter Description");
                    if (title) {
                        $.ajax({
                            url: 'save_task.php',
                            data: {
                                title: title,
                                start: moment(start).format("YYYY-MM-DD HH:mm:ss"),
                                end: moment(end).format("YYYY-MM-DD HH:mm:ss"),
                                priority: priority,
                                description: description
                            },
                            type: 'POST',
                            success: function() {
                                alert("Task Saved");
                                $('#calendar').fullCalendar('refetchEvents');
                            }
                        });
                    }
                    $('#calendar').fullCalendar('unselect');
                }
            });
        });
    </script>
</body>
</html>
