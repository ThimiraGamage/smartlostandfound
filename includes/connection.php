<?php
<<<<<<< HEAD
$conn = new mysqli("localhost", "root", "", "smartlostandfound", 3306);
=======
$conn = new mysqli("localhost", "root", "Nadu@20031124", "smartlostandfound", 3306);
>>>>>>> 5e1df5d31e9597f903c126600b80edc9390abf10

if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
