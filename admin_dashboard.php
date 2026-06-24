<?php
include 'includes/connection.php';

// 2. Get Total Users Count
$sql_users = "SELECT COUNT(*) as total_users FROM users";
$result_users = mysqli_query($conn, $sql_users);
$row_users = mysqli_fetch_assoc($result_users);
$total_users_count = $row_users['total_users'];

// 3. Get Total Items Count
$sql_items = "SELECT COUNT(*) as total_items FROM items";
$result_items = mysqli_query($conn, $sql_items);
$row_items = mysqli_fetch_assoc($result_items);
$total_items_count = $row_items['total_items'];

// 4. Get Pending Claims Count (Where claim_status is 'Pending')
$sql_claims = "SELECT COUNT(*) as pending_claims FROM claims WHERE claim_status = 'Pending'";
$result_claims = mysqli_query($conn, $sql_claims);
$row_claims = mysqli_fetch_assoc($result_claims);
$pending_claims_count = $row_claims['pending_claims'];

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Lost & Found</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            Admin Panel
        </div>

        <a href="admin_dashboard.php" class="nav-item active">
            <span class="material-symbols-outlined">dashboard</span>
            Dashboard
        </a>
        <a href="admin_users.php" class="nav-item">
            <span class="material-symbols-outlined">group</span>
            Users
        </a>
        <a href="admin_items.php" class="nav-item">
            <span class="material-symbols-outlined">inventory_2</span>
            Items
        </a>
        <a href="#" class="nav-item">
            <span class="material-symbols-outlined">assignment</span>
            Claims
        </a>
    </div>

    <div class="main-content">
        
        <div class="header">
            <h1>Overview</h1>
            <a href="login.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-cards">
            
            <div class="card">
                <div class="card-info">
                    <h3>Total Users</h3>
                    <h2><?php echo $total_users_count; ?></h2>
                </div>
                <div class="card-icon">
                    <span class="material-symbols-outlined">group</span>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Items</h3>
                    <h2><?php echo $total_items_count; ?></h2>
                </div>
                <div class="card-icon">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Pending Claims</h3>
                    <h2><?php echo $pending_claims_count; ?></h2>
                </div>
                <div class="card-icon">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>

        </div>

    </div>

</body>
</html>