<?php
session_start();
include('db.php');

$user_id = $_SESSION['user_id']; // Assuming user is logged in

// Fetch user data from the database
$query = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result); // Store user info in $user variable
} else {
    echo "Error fetching user data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .popup-success {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            width: fit-content;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Settings</h2>

        <!-- Profile Settings -->
        <form action="save_settings.php" method="POST">
            <h3>Profile Settings</h3>

            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>

            <!-- Notification Settings -->
            <h3>Notification Settings</h3>
            <label for="notification_method">Notification Method:</label>
            <select name="notification_method">
                <option value="Email" <?php echo ($user['notification_method'] == 'Email') ? 'selected' : ''; ?>>Email</option>
                <option value="WhatsApp" <?php echo ($user['notification_method'] == 'WhatsApp') ? 'selected' : ''; ?>>WhatsApp</option>
                <option value="In-App" <?php echo ($user['notification_method'] == 'In-App') ? 'selected' : ''; ?>>In-App</option>
            </select>

            <!-- Save Button -->
            <input type="submit" class="submit-button" value="Save Settings">
        </form>

        <!-- Popup for 'Settings Saved Successfully' -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="popup-success">
                Settings saved successfully!
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
