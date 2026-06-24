<?php
include 'includes/connection.php';

$sql = "SELECT claims.claim_id,
               claims.message,
               claims.claim_status,
               claims.created_at,
               items.item_name,
               users.full_name AS claimant_name,
               users.email AS claimant_email
        FROM claims
        LEFT JOIN items ON claims.item_id = items.item_id
        LEFT JOIN users ON claims.claimant_id = users.user_id
        ORDER BY claims.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Claims - Admin</title>

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
        <a href="admin_items.php" class="nav-item">
            <span class="material-symbols-outlined">inventory_2</span>
            Items
        </a>
        <a href="admin_claims.php" class="nav-item active">
            <span class="material-symbols-outlined">assignment</span>
            Claims
        </a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Claims</h1>
            <a href="login.php" class="logout-btn">Logout</a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Claim ID</th>
                    <th>Item Name</th>
                    <th>Claimant</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['claim_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['item_name'] ?? 'Unknown Item'); ?></td>
                            <td><?php echo htmlspecialchars($row['claimant_name'] ?? 'Unknown User'); ?></td>
                            <td><?php echo htmlspecialchars($row['claimant_email'] ?? 'Unknown Email'); ?></td>
                            <td><?php echo htmlspecialchars($row['claim_status']); ?></td>
                            <td><?php echo htmlspecialchars($row['message'] ?? ''); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No claims found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>
</html>