
<?php
session_start();

// Database connection
$connection = mysqli_connect("localhost", "root", "", "fyp");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $frequency = $_POST['frequency'];
    $time = $_POST['time'];
    $notification_method = $_POST['notification_method'];

    // Handle custom frequency options
    if ($frequency == 'custom') {
        $custom_days = isset($_POST['custom_days']) ? implode(', ', $_POST['custom_days']) : 'None';
        $custom_date = $_POST['custom_date'] ?? 'N/A';
    } else {
        $custom_days = 'N/A';
        $custom_date = 'N/A';
    }

    // Insert reminder into the database
    $query = "INSERT INTO reminders (frequency, time, notification_method, custom_days, custom_date) VALUES ('$frequency', '$time', '$notification_method', '$custom_days', '$custom_date')";
    
    if (mysqli_query($connection, $query)) {
        $_SESSION['success'] = "Reminder saved successfully!";
    } else {
        $_SESSION['error'] = "Failed to save reminder: " . mysqli_error($connection);
    }

    // Close the database connection
    mysqli_close($connection);

    // Redirect back to the reminders.php page
    header("Location: reminders.php");
    exit();
}

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
    $mail->Username = 'your-email@example.com'; // Set your SMTP username
    $mail->Password = 'your-email-password';   // Set your SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('your-email@example.com', 'Calendify');
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
$reminderText = $_POST['reminder_text'];  // Get the reminder text
$reminderTime = $_POST['reminder_time'];  // Get the reminder time

// Twilio credentials
$sid = 'YOUR_TWILIO_ACCOUNT_SID';  // Replace with your Twilio SID
$token = 'YOUR_TWILIO_AUTH_TOKEN';  // Replace with your Twilio Auth Token
$twilio = new Client($sid, $token);

// Send WhatsApp notification if selected
if ($notificationMethod == 'WhatsApp') {
    try {
        $message = $twilio->messages->create(
            'whatsapp:+' . $userPhoneNumber,  // User’s WhatsApp number
            [
                'from' => 'whatsapp:+14155238886',  // Your Twilio WhatsApp number
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
$query = "SELECT * FROM reminders WHERE user_id = $user_id AND reminder_time > NOW()";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<div class='notification'>";
    echo "Reminder: " . $row['reminder_title'] . " is scheduled for " . $row['reminder_time'];
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

function sendWhatsAppReminder($userPhoneNumber, $reminderTime) {
    // Add your Twilio WhatsApp API logic here
}

