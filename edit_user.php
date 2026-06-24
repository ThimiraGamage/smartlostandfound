<?php
include 'includes/connection.php';

// Check if an ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$user_id = $_GET['id'];
$success_msg = "";
$error_msg = "";

// Handle Form Submission
if (isset($_POST['update_user'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Update query
    $update_sql = "UPDATE users SET full_name = '$full_name', email = '$email' WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        $success_msg = "User updated successfully!";
    } else {
        $error_msg = "Error updating user: " . mysqli_error($conn);
    }
}

// Fetch current user data to pre-fill the form
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: admin_users.php");
    exit();
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            Admin Panel
        </div>

        <a href="admin_dashboard.php" class="nav-item">
            <span class="material-symbols-outlined">dashboard</span>
            Dashboard
        </a>
        <a href="admin_users.php" class="nav-item active">
            <span class="material-symbols-outlined">group</span>
            Users
        </a>
        <a href="admin_items.php" class="nav-item">
            <span class="material-symbols-outlined">inventory_2</span>
            Items
        </a>
        <a href="admin_claims.php" class="nav-item">
            <span class="material-symbols-outlined">assignment</span>
            Claims
        </a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Edit User</h1>
            <a href="admin_users.php" class="logout-btn" style="background-color: var(--primary-light); color: var(--primary);">
                <span class="material-symbols-outlined">arrow_back</span>
                Back to Users
            </a>
        </div>

        <div class="data-table-container form-container" style="max-width: 600px;">
            
            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success" style="background: var(--success-bg); color: var(--success); padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger" style="background: var(--danger-bg); color: var(--danger); padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="admin-form">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['user_id']); ?>" disabled class="form-control disabled">
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="form-control">
                </div>

                <button type="submit" name="update_user" class="btn-submit">Update User</button>
            </form>
        </div>

    </div>

</body>
</html>