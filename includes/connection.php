<?php
// Establish the connection using the procedural function
$conn = mysqli_connect("localhost", "root", "", "smartlostfound", 3306);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>