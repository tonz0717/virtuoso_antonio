<?php
// Database connection
$host = 'localhost'; // Your DB host
$db = 'benibratz'; // Your DB name
$user = 'root'; // Your DB username
$pass = '';

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}