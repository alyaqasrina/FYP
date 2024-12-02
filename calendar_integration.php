<?php

require_once __DIR__ . '/vendor/autoload.php'; // Adjust the path if needed

$client = new Google_Client(); // The existing line that caused the error

$client->setClientId('722383716668-fepo5slga7efjk0bd2120djsdmrg2jds.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/oauth2callback');
$client->addScope(Google_Service_Calendar::CALENDAR);

// Authenticate user
if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: ' . filter_var('calendar_integration.php', FILTER_SANITIZE_URL));
    exit();
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Calendar($client);

    // Example: Fetching upcoming events
    $calendarId = 'primary';
    $optParams = array(
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c'),
    );

    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();

    if (empty($events)) {
        echo 'No upcoming events found.';
    } else {
        echo "Upcoming events:<br>";
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            echo $event->getSummary() . " (" . $start . ")<br>";
        }
    }
} else {
    echo "Authentication required.";
}
?>
