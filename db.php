<?php
    $servername = "localhost"; // or your database server name
    $username = "root"; // your database username
    $password = ""; // your database password
    $dbname = "fyp"; // your database name

    $conn = mysqli_connect("localhost","root","","fyp");
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
?>