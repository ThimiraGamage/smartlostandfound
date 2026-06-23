<?php

session_start();

include 'includes/connection.php';

/* GET ITEM ID FROM URL */

$item_id = (int)($_GET['item_id'] ?? 0);

if(!$item_id)
{
    die("Invalid Item ID");
}

/* GET ITEM + FINDER DETAILS */

$sql = "SELECT items.*, 
        users.full_name, 
        users.email

        FROM items

        JOIN users 
        ON items.user_id = users.user_id

        WHERE items.item_id = ?";

$stmt = $conn->prepare($sql);

if(!$stmt)
{
    die("Database Error: " . $conn->error);
}

$stmt->bind_param("i", $item_id);
$stmt->execute();

$result = $stmt->get_result();

if(!$result || $result->num_rows == 0)
{
    die("Item Not Found");
}

$row = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Item</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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
                src="<?php echo $row['image']; ?>"
                class="item-image"
                alt="Item Image"
            >

            <h2>

                <?php echo $row['item_name']; ?>

            </h2>

            <p>

                <strong>Category:</strong>

                <?php echo $row['category']; ?>

            </p>

            <p>

                <strong>Location:</strong>

                <?php echo $row['location']; ?>

            </p>

            <p>

                <strong>Date:</strong>

                <?php echo $row['created_at']; ?>

            </p>

            <p>

                <strong>Description:</strong>

                <?php echo $row['description']; ?>

            </p>

        </div>

        <!-- FINDER DETAILS -->

        <div class="finder-details">

            <h3>

                Finder Information

            </h3>

            <p>

                <strong>Name:</strong>

                <?php echo $row['full_name']; ?>

            </p>

            <p>

                <strong>Email:</strong>

                <?php echo $row['email']; ?>

            </p>

            <p>

                <strong>Email:</strong>

                <?php echo $row['email']; ?>

            </p>

            <a 
                href="mailto:<?php echo $row['email']; ?>?subject=Regarding%20the%20item%20<?php echo urlencode($row['item_name']); ?>" 
                target="_blank"
                class="whatsapp-btn"
                >

                    Contact via Email

            </a>
        </div>

        

        

    </div>

</section>

</body>

</html>