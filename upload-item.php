<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);

if (!$is_logged_in) {
    header("Location: login.php");
    exit();
}

// Handle form submission
$conn = new mysqli("localhost", "root", "", "smartlostfound", 3306);

if($conn->connect_error) {
    die("Connection Failed : " . $conn->connect_error);
}

$message = "";
$messageType = "";

if (isset($_POST['submit_item'])) {
    $item_name = $_POST['item_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $location = $_POST['location'] ?? '';
    $item_date = $_POST['item_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $item_type = $_POST['item_type'] ?? 'Lost';
    
    $user_id = $_SESSION['user_id'];

    if (empty($item_name) || empty($category) || empty($location) || empty($item_date) || empty($description)) {
        $message = "All fields are required!";
        $messageType = "error";
    } else {
        // Handle file upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array($filetype, $allowed)) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $new_filename = uniqid() . '.' . $filetype;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
                }
            }
        }

        // Insert into database
        $sql = "INSERT INTO items (user_id, item_name, category, location, item_date, description, item_type, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $user_id, $item_name, $category, $location, $item_date, $description, $item_type, $image_path);

        if ($stmt->execute()) {
            $message = "Item reported successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Item</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/upload-item.css">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <!-- FORM SECTION -->

    <section class="upload-section">

        <div class="upload-container">
           
            <h1>Report Lost / Found Item</h1>

            <p>Fill the details below to report your item.</p>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- ITEM NAME -->
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" placeholder="Enter item name" required>
                </div>

                <!-- CATEGORY -->
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Bag">Bag</option>
                        <option value="ID Card">ID Card</option>
                        <option value="Calculator">Calculator</option>
                        <option value="Phone">Phone</option>
                        <option value="Wallet">Wallet</option>
                        <option value="Keys">Keys</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- LOCATION -->
                <div class="form-group">
                    <label>Location *</label>
                    <input type="text" name="location" placeholder="Enter location where item was lost/found" required>
                </div>

                <!-- DATE -->
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="item_date" required>
                </div>

                <!-- DESCRIPTION -->
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" placeholder="Describe the item in detail" rows="4" required></textarea>
                </div>

                <!-- ITEM TYPE -->
                <div class="form-group">
                    <label>Item Type *</label>
                    <select name="item_type" required>
                        <option value="Lost">Lost Item</option>
                        <option value="Found">Found Item</option>
                    </select>
                </div>

                <!-- IMAGE -->
                <div class="form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <!-- BUTTON -->
                <button type="submit" name="submit_item" class="submit-btn">

                    Submit Report

                </button>

            </form>

        </div>

    </section>

</body>

</html>