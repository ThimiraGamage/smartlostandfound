<?php

session_start();

$conn = new mysqli("localhost", "root", "", "smartlostfound", 3306);

if($conn->connect_error)
{
    die("Connection Failed : " . $conn->connect_error);
}

/* GET FORM DATA */

$item_name = $_POST['item_name'];
$category = $_POST['category'];
$location = $_POST['location'];
$date = $_POST['date'];
$description = $_POST['description'];
$item_type = $_POST['item_type'];

$user_id = $_SESSION['user_id'];

/* IMAGE UPLOAD */

$imageName = $_FILES['item_image']['name'];
$tempName = $_FILES['item_image']['tmp_name'];

$uploadFolder = "uploads/";

/* CREATE UNIQUE IMAGE NAME */

$newImageName = time() . "_" . $imageName;

/* FINAL IMAGE PATH */

$imagePath = $uploadFolder . $newImageName;

/* MOVE IMAGE TO UPLOADS FOLDER */

move_uploaded_file($tempName, $imagePath);

/* INSERT INTO DATABASE */

$sql = "INSERT INTO items
(
    item_name,
    category,
    location,
    date,
    description,
    item_type,
    image,
    user_id
)

VALUES
(
    '$item_name',
    '$category',
    '$location',
    '$date',
    '$description',
    '$item_type',
    '$imagePath',
    '$user_id'
)";

if($conn->query($sql) === TRUE)
{
    echo "
    <script>
        alert('Item Report Submitted Successfully!');
        window.location.href='dashboard.php';
    </script>
    ";
}
else
{
    echo "Error : " . $conn->error;
}

?>