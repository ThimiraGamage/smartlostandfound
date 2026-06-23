<?php

session_start();

include 'includes/connection.php';

/* GET ITEM ID FROM URL */

$item_id = $_GET['item_id'] ?? null;

if (!$item_id) {
    die("Invalid Item ID");
}


$sql = "SELECT items.*, users.full_name, users.email, users.phone_number
        FROM items
        JOIN users ON items.user_id = users.user_id
        WHERE items.item_id = '$item_id'";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Item Not Found");
}

$row = mysqli_fetch_assoc($result);

/* SUBMIT FOUND REPORT */

$message = "";

if (isset($_POST['submit_found'])) {

    $finder_name = $_POST['finder_name'];
    $finder_email = $_POST['finder_email'];
    $finder_phone = $_POST['finder_phone'];
    $location_found = $_POST['location_found'];
    $date_found = $_POST['date_found'];
    $found_message = $_POST['found_message'];

    $insert = "INSERT INTO found_reports
    (
        item_id,
        finder_name,
        finder_email,
        finder_phone,
        location_found,
        date_found,
        found_message
    )
    VALUES
    (
        '$item_id',
        '$finder_name',
        '$finder_email',
        '$finder_phone',
        '$location_found',
        '$date_found',
        '$found_message'
    )";

    if (mysqli_query($conn, $insert)) {
        $message = "Found Report Submitted Successfully!";
    } else {
        $message = "Failed to Submit Found Report!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item</title>
    <link rel="stylesheet" href="assets/css/claim-item.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<?php
$is_logged_in = isset($_SESSION['user_id']);
?>

<section class="claim-page">

    <div class="claim-container">

        <!-- ITEM DETAILS -->
        <div class="item-details">

            <img 
                src="<?php echo $row['image'] ?? ''; ?>"
                class="item-image"
                alt="Item Image"
            >

            <h2><?php echo $row['item_name'] ?? ''; ?></h2>

            <p><strong>Category:</strong> <?php echo $row['category'] ?? ''; ?></p>

            <p><strong>Location Lost:</strong> <?php echo $row['location'] ?? ''; ?></p>

            <p><strong>Date Lost:</strong> <?php echo $row['created_at'] ?? ''; ?></p>

            <p><strong>Description:</strong> <?php echo $row['description'] ?? ''; ?></p>

        </div>

        <!-- OWNER DETAILS -->
        <div class="finder-details">

            <h3>Owner Information</h3>

            <p><strong>Name:</strong> <?php echo $row['full_name'] ?? ''; ?></p>

            <p><strong>Email:</strong> <?php echo $row['email'] ?? ''; ?></p>

            <p><strong>Phone:</strong> <?php echo $row['phone_number'] ?? ''; ?></p>

            <a 
                href="https://wa.me/<?php echo $row['phone_number'] ?? ''; ?>?text=Hello%20I%20found%20your%20item%20<?php echo urlencode($row['item_name'] ?? ''); ?>" 
                target="_blank"
                class="whatsapp-btn"
            >
                Contact Owner on WhatsApp
            </a>

        </div>

    </div>

</section>

</body>
</html>