<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/connection.php';

$message = "";
$messageType = "";

/* CATEGORY → MODELS */
$models = array(
    "Phone" => array("iPhone 15", "iPhone 14", "Samsung S24", "OnePlus 12"),
    "Laptop" => array("MacBook Air M2", "Dell XPS 13", "HP Pavilion", "Lenovo ThinkPad"),
    "Bag" => array("Backpack", "School Bag", "Travel Bag"),
    "Calculator" => array("Casio FX-991", "Sharp EL-506"),
    "Wallet" => array("Leather Wallet", "Card Holder"),
    "Keys" => array("House Keys", "Car Keys")
);

if (isset($_POST['submit_item'])) {

    $user_id     = $_SESSION['user_id'];
    $item_name   = $_POST['item_name'] ?? '';
    $category    = $_POST['category'] ?? '';
    $model       = $_POST['model'] ?? '';
    $model_other = $_POST['model_other'] ?? '';
    $location    = $_POST['location'] ?? '';
    $item_date   = $_POST['item_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $item_type   = $_POST['item_type'] ?? 'Lost';
    $color = $_POST['color'] ?? '';
    /* FINAL MODEL */
    if ($model === "Other") {
        $final_model = $model_other;
    } else {
        $final_model = $model;
    }

    if ($item_name == "" || $category == "" || $location == "" || $item_date == "" || $color == "") {
        $message = "Required fields missing!";
        $messageType = "error";
    } else {

        /* IMAGE UPLOAD */
        $image_path = "";

        if (!empty($_FILES['image']['name'])) {

            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {

                $upload_dir = "uploads/";

                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $new_name = uniqid() . "." . $ext;
                $target = $upload_dir . $new_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $image_path = $target;
                }
            }   
        }

        /* STORE MODEL INSIDE DESCRIPTION (your DB has no model column) */
        $final_description = "Model: " . $final_model .
                     " | Color: " . $color .
                     " | " . $description;

        /* PROCEDURAL INSERT */
        $sql = "INSERT INTO items 
        (user_id, item_name, category, location, item_date, description, item_type, image)
        VALUES 
        ('$user_id', '$item_name', '$category', '$location', '$item_date', '$final_description', '$item_type', '$image_path')";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $message = "Item reported successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $messageType = "error";
        }
    }
}
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

<section class="upload-section">
<div class="upload-container">

<h1>Report Lost / Found Item</h1>

<p>Fill the details below to report your item.</p>

<?php if ($message != "") { ?>
    <div class="message <?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

    <!-- ITEM NAME -->
    <div class="form-group">
        <label>Item Name *</label>
        <input type="text" name="item_name" required>
    </div>

    <!-- CATEGORY -->
    <div class="form-group">
        <label>Category *</label>
        <select name="category" id="category" onchange="loadModels()" required>
            <option value="">Select Category</option>

            <?php
            foreach ($models as $cat => $list) {
                echo "<option value='$cat'>$cat</option>";
            }
            ?>

        </select>
    </div>

    <!-- MODEL -->
    <div class="form-group">
        <label>Model *</label>
        <select name="model" id="model" onchange="checkOther()" required>
            <option value="">Select Model</option>
        </select>

        <input type="text" name="model_other" id="model_other"
               placeholder="Enter model manually"
               style="display:none; margin-top:10px;">
    </div>

    <!-- COLOR -->
    <div class="form-group">
        <label>Color *</label>
        <select name="color" required>
            <option value="">Select Color</option>
            <option value="Black">Black</option>
            <option value="White">White</option>
            <option value="Blue">Blue</option>
            <option value="Red">Red</option>
            <option value="Green">Green</option>
            <option value="Grey">Grey</option>
            <option value="Brown">Brown</option>
            <option value="Silver">Silver</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <!-- LOCATION -->
    <div class="form-group">
        <label>Location *</label>
        <input type="text" name="location" required>
    </div>

    <!-- DATE -->
    <div class="form-group">
        <label>Date *</label>
        <input type="date" name="item_date" required>
    </div>

    <!-- DESCRIPTION -->
    <div class="form-group">
        <label>Description *</label>
        <textarea name="description" required></textarea>
    </div>

    <!-- ITEM TYPE -->
    <div class="form-group">
        <label>Item Type *</label>
        <select name="item_type">
            <option value="Lost">Lost</option>
            <option value="Found">Found</option>
        </select>
    </div>

    <!-- IMAGE -->
    <div class="form-group">
        <label>Upload Image</label>
        <input type="file" name="image">
    </div>

    <!-- SUBMIT -->
    <button type="submit" name="submit_item" class="submit-btn">
        Submit Item
    </button>

</form>

</div>
</section>

<script>

var models = <?php echo json_encode($models); ?>;

function loadModels() {

    var category = document.getElementById("category").value;
    var model = document.getElementById("model");

    model.innerHTML = "<option value=''>Select Model</option>";

    if (models[category]) {

        for (var i = 0; i < models[category].length; i++) {
            var opt = document.createElement("option");
            opt.value = models[category][i];
            opt.text = models[category][i];
            model.appendChild(opt);
        }

        var other = document.createElement("option");
        other.value = "Other";
        other.text = "Other";
        model.appendChild(other);
    }
}

function checkOther() {

    var model = document.getElementById("model").value;
    var input = document.getElementById("model_other");

    if (model === "Other") {
        input.style.display = "block";
    } else {
        input.style.display = "none";
        input.value = "";
    }
}

</script>

</body>
</html>