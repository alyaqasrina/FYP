<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Settings</title>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .container {
            max-width: 600px;
        }
        /* Hide the custom day/date fields initially */
        #custom-fields {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Reminder Settings</h1>

        <!-- Display success or error message if set -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); // Clear the success message ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); // Clear the error message ?>
        <?php endif; ?>

        <form action="save_reminder.php" method="POST">
            <!-- Frequency Setting -->
            <div class="mb-3">
                <label for="frequency" class="form-label">Reminder Frequency</label>
                <select class="form-select" id="frequency" name="frequency" required>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            <!-- Time Setting -->
            <div class="mb-3">
                <label for="time" class="form-label">Reminder Time</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>

            <!-- Custom Days/Date Fields (Initially Hidden) -->
            <div id="custom-fields">
                <!-- Days of the Week (Checkboxes) -->
                <div class="mb-3">
                    <label class="form-label">Custom Days (Select days)</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Monday">
                        <label class="form-check-label">Monday</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Tuesday">
                        <label class="form-check-label">Tuesday</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Wednesday">
                        <label class="form-check-label">Wednesday</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Thursday">
                        <label class="form-check-label">Thursday</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Friday">
                        <label class="form-check-label">Friday</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Saturday">
                        <label class="form-check-label">Saturday</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="custom_days[]" value="Sunday">
                        <label class="form-check-label">Sunday</label>
                    </div>
                </div>

                <!-- Custom Date (Optional) -->
                <div class="mb-3">
                    <label for="custom_date" class="form-label">Custom Date (Optional)</label>
                    <input type="date" class="form-control" id="custom_date" name="custom_date">
                </div>
            </div>

            <!-- Notification Method -->
            <div class="mb-3">
                <label for="notification_method" class="form-label">Notification Method</label>
                <select class="form-select" id="notification_method" name="notification_method" required>
                    <option value="email">Email</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="in-app">In-app</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Save Reminder</button>
            </div>
        </form>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <!-- JavaScript to Show/Hide Custom Fields -->
    <script>
        document.getElementById('frequency').addEventListener('change', function() {
            var customFields = document.getElementById('custom-fields');
            if (this.value === 'custom') {
                customFields.style.display = 'block';
            } else {
                customFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>


