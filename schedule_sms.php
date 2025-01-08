<?php

function schedule_sms($phone, $message, $scheduled_time)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'https://terminal.adasms.com/api/v1/send');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $data = array(
        '_token' => getenv("SMS_API_SECRET"),
        'phone' => $phone,
        'message' => $message,
        'callback_url' => 'https://eojt58wj892oecx.m.pipedream.net/',
        'send_at' => $scheduled_time
    );

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
}
