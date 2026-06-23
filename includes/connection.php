<?php

$conn = new mysqli("localhost", "root", "Nadu@20031124", "smartlostandfound", 3306);


if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
