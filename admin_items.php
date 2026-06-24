<?php
// 1. Database Connection
include 'includes/connection.php';

$sql = "SELECT items.*, users.full_name 
        FROM items 
        LEFT JOIN users ON items.user_id = users.user_id 
        ORDER BY items.created_at DESC";
        
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="assets/css/admin_users.css">
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
        <a href="admin_users.php" class="nav-item">
            <span class="material-symbols-outlined">group</span>
            Users
        </a>
        <a href="admin_items.php" class="nav-item active">
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
            <h1>Reported Items</h1>
            <a href="login.php" class="logout-btn">Logout</a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Reported By</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 3. Loop through the fetched database records
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['item_id'] . "</td>";
                        echo "<td>" . $row['item_name'] . "</td>";
                        
                        // Check if item is Lost or Found to apply the correct CSS badge color
                        $badgeClass = ($row['item_type'] == 'Lost') ? 'badge-lost' : 'badge-found';
                        echo "<td><span class='badge " . $badgeClass . "'>" . $row['item_type'] . "</span></td>";
                        
                        echo "<td>" . $row['category'] . "</td>";
                        
                        // Display user's name (handled by the JOIN query)
                        $userName = $row['full_name'] ? $row['full_name'] : 'Unknown User';
                        echo "<td>" . $userName . "</td>";
                        
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['item_date'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No items reported yet.</td></tr>";
                }
                
                // Close connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>

    </div>

</body>
</html>