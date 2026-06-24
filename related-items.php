<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/connection.php';

if (!isset($_GET['item_id'])) {
    die("Invalid Item.");
}

$item_id = $_GET['item_id'];

$sql = "SELECT * FROM items WHERE item_id='$item_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Item not found.");
}

$item = mysqli_fetch_assoc($result);

if ($item['item_type'] == "Lost") {
    $search_type = "Found";
} else {
    $search_type = "Lost";
}

$match_sql = "SELECT *,
(
    (CASE WHEN category = '" . $item['category'] . "' THEN 40 ELSE 0 END) +
    (CASE WHEN color = '" . $item['color'] . "' THEN 30 ELSE 0 END) +
    (CASE WHEN model = '" . $item['model'] . "' THEN 20 ELSE 0 END) +
    (CASE WHEN item_name LIKE '%" . $item['item_name'] . "%' THEN 10 ELSE 0 END)
) AS match_score
FROM items
WHERE item_type='$search_type'
AND item_id != '$item_id'
HAVING match_score > 30
ORDER BY match_score DESC, created_at DESC";

$matches = mysqli_query($conn, $match_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Related Items</title>
<link rel="stylesheet" href="assets/css/related-items.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">


</head>

<body>
<?php include 'includes/navbar.php'; ?>

<!-- Success Banner -->
<div class="success-banner">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
  </svg>
  <p>Your item has been uploaded successfully.</p>
</div>

<?php if (mysqli_num_rows($matches) > 0): ?>

  <div class="section-header">
    <h2>Possible Matching Items</h2>
    <p>These items were found in our system based on category, color, and model.</p>
  </div>

  <div class="related-grid">
    <?php while ($row = mysqli_fetch_assoc($matches)):
      $score = (int) $row['match_score'];
      $badge_class = $score >= 70 ? 'high' : ($score >= 50 ? 'medium' : 'low');
    ?>
    <div class="card">

      <div class="card-img-wrap">
        <?php if (!empty($row['image'])): ?>
          <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['item_name']) ?>">
        <?php else: ?>
          <div class="no-img">No image available</div>
        <?php endif; ?>

        <span class="match-badge <?= $badge_class ?>">
          <?= $score ?>% Match
        </span>
      </div>

      <div class="card-body">
        <h3><?= htmlspecialchars($row['item_name']) ?></h3>

        <div class="meta-list">
          <div class="meta-row">
            <span class="meta-label">Category</span>
            <span class="meta-value"><?= htmlspecialchars($row['category']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Model</span>
            <span class="meta-value"><?= htmlspecialchars($row['model']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Color</span>
            <span class="meta-value"><?= htmlspecialchars($row['color']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Location</span>
            <span class="meta-value"><?= htmlspecialchars($row['location']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Date</span>
            <span class="meta-value"><?= htmlspecialchars($row['item_date']) ?></span>
          </div>
        </div>

        <a class="view-btn" href="claim-item.php?item_id=<?php echo $row['item_id']; ?>">
          View Item →
        </a>
      </div>

    </div>
    <?php endwhile; ?>
  </div>

<?php else: ?>

  <div class="section-header">
    <h2>Possible Matching Items</h2>
  </div>

  <div class="empty-state">
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
    </svg>
    <h3>No matches found yet</h3>
    <p>Nobody has reported a matching item so far. Check back later — matches are updated as new items are reported.</p>
  </div>

<?php endif; ?>

</body>
</html>