<?php
include 'includes/connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-logo">
        <span class="material-symbols-outlined">admin_panel_settings</span>
        Admin Panel
    </div>

    <a href="admin_dashboard.php" class="nav-item">Dashboard</a>
    <a href="admin_users.php" class="nav-item active">Users</a>
    <a href="admin_items.php" class="nav-item">Items</a>
    <a href="admin_claims.php" class="nav-item">Claims</a>
</div>

<!-- MAIN -->
<div class="main-content">

    <div class="header">
        <h1>Registered Users</h1>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>

    <div class="page-container">

        <div class="table-card">

            <table class="data-table">

                <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <tr>
                            <td><?= $row['user_id'] ?></td>

                            <td><?= htmlspecialchars($row['full_name']) ?></td>

                            <td class="email"><?= htmlspecialchars($row['email']) ?></td>

                            <td><?= $row['created_at'] ?></td>

                            <td>
                                <div class="action-btns">

                                    <a href="edit_user.php?user_id=<?= $row['user_id'] ?>"
                                       class="btn edit">Edit</a>

                                    <form action="delete_user.php" method="POST"
                                          onsubmit="return confirm('Delete this user?');">

                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">

                                        <button class="btn delete">Delete</button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="empty">No users found</td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>