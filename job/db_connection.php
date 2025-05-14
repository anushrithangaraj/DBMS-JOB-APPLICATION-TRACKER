<?php
// Replace these with your actual database credentials
$servername = "localhost";
$username = "root"; // Default username for XAMPP is 'root'
$password = ""; // Default password for XAMPP is empty
$dbname = "job"; // Change to your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

