
<?php
session_start();
include 'db.php';
require_once 'priority_utils.php';

// Check user session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "User not logged in!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $frequency = $_POST['frequency'];
    $time = $_POST['time'];
    $notification_method = $_POST['notification_method'];

    if ($frequency == 'custom') {
        $custom_days = isset($_POST['custom_days']) ? implode(', ', $_POST['custom_days']) : 'None';
        $custom_date = $_POST['custom_date'] ?? 'N/A';
    } else {
        $custom_days = 'N/A';
        $custom_date = 'N/A';
    }
    // Insert reminder
    $query = "INSERT INTO reminders (user_id, frequency, time, notification_method, custom_days, custom_date) 
              VALUES ('$user_id', '$frequency', '$time', '$notification_method', '$custom_days', '$custom_date')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Reminder saved successfully!";
    } else {
        $_SESSION['error'] = "Failed to save reminder: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: reminders.php");
    exit();

}

// Notify user about the top-priority task
$tasks = getSortedTasks($connection, date('Y-m-d'));
if (!empty($tasks)) {
    $topTask = $tasks[0];
    echo "Reminder: Your top priority task for today is: " . $topTask['title'];

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';  // Set your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = '@gmail.com'; // Set your SMTP username
    $mail->Password = '';   // Set your SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('@gmail.com', 'Calendify');
    $mail->addAddress($user_email);     // Add user's registered email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Reminder Saved';
    $mail->Body    = 'You have successfully saved a new reminder for ' . $reminder_time;

    $mail->send();
    echo 'Reminder saved successfully! Email sent.';
} catch (Exception $e) {
    echo "Reminder saved but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

require __DIR__ . '/vendor/autoload.php';  // Load Twilio SDK

use Twilio\Rest\Client;

// Collect form data
$userPhoneNumber = $_POST['phone_number'];  // Get the user's phone number from the form
$notificationMethod = $_POST['notification_method'];  // Get the notification method (e.g., 'WhatsApp')
$reminderText = $_POST['reminder_name'];  // Get the reminder text
$reminderTime = $_POST['time'];  // Get the reminder time

// Twilio credentials
$sid = '';  // Replace with your Twilio SID
$token = '';  // Replace with your Twilio Auth Token
$twilio = new Client($sid, $token);

// Send WhatsApp notification if selected
if ($notificationMethod == 'WhatsApp') {
    try {
        $message = $twilio->messages->create(
            'WhatsApp:+' . $userPhoneNumber,  // User’s WhatsApp number
            [
                'from' => 'SMS:+12316362611',  // Your Twilio WhatsApp number
                'body' => "Reminder: $reminderText at $reminderTime"
            ]
        );
        echo "WhatsApp message sent successfully!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Save reminder to the database (your existing code)
echo "Reminder saved successfully!";



// Fetch upcoming reminders from the database
$query = "SELECT * FROM reminders WHERE user_id = $user_id AND time > NOW()";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<div class='notification'>";
    echo "Reminder: " . $row['reminder_name'] . " is scheduled for " . $row['reminder_time'];
    echo "</div>";
}


$notification_method = $_POST['notification_method'];

if ($notification_method == 'Email') {
    // Call Email function
} elseif ($notification_method == 'WhatsApp') {
    // Call WhatsApp function
} elseif ($notification_method == 'In-App') {
    // No need to do anything extra, the reminder will be saved in the database and shown in-app
}

// Functions for sending reminders
function sendEmailReminder($userEmail, $reminderTime) {
    // Add your email sending logic using PHPMailer here
}

function sendSMSReminder($userPhoneNumber, $reminderTime) {
    // Add your Twilio SMS API logic here
}

