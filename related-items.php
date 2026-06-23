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

/* GET THE NEWLY UPLOADED ITEM */

$sql = "SELECT * FROM items WHERE item_id='$item_id'";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Item not found.");
}

$item = mysqli_fetch_assoc($result);

/* Determine opposite item type */

if($item['item_type']=="Lost")
{
    $search_type="Found";
}
else
{
    $search_type="Lost";
}

/* FIND MATCHES */
$match_sql = "SELECT *,
(
    (CASE WHEN category = '".$item['category']."' THEN 40 ELSE 0 END) +
    (CASE WHEN color = '".$item['color']."' THEN 30 ELSE 0 END) +
    (CASE WHEN model = '".$item['model']."' THEN 20 ELSE 0 END) +
    (CASE WHEN item_name LIKE '%".$item['item_name']."%' THEN 10 ELSE 0 END)
) AS match_score

FROM items

WHERE item_type='$search_type'
AND item_id != '$item_id'

HAVING match_score > 30

ORDER BY match_score DESC, created_at DESC";
$matches = mysqli_query($conn,$match_sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Related Items</title>

<link rel="stylesheet" href="assets/css/upload-item.css">

<style>

.related-container{

width:90%;
max-width:1000px;
margin:40px auto;

}

.related-grid{

display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;

}

.card{

border:1px solid #ddd;
border-radius:10px;
padding:15px;
background:#fff;

}

.card img{

width:100%;
height:220px;
object-fit:cover;
border-radius:8px;

}

.card h3{

margin:10px 0;

}

.view-btn{

display:inline-block;
padding:10px 18px;
background:#007BFF;
color:white;
text-decoration:none;
border-radius:5px;

}

.view-btn:hover{

background:#0056b3;

}

.success{

text-align:center;
color:green;
margin-bottom:30px;

}

</style>

</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="related-container">

<h2 class="success">

Your item has been uploaded successfully.

</h2>

<h2>

Possible Matching Items

</h2>

<?php

if(mysqli_num_rows($matches)>0)
{

?>

<div class="related-grid">

<?php

while($row=mysqli_fetch_assoc($matches))
{

?>

<div class="card">

<?php

if($row['image']!="")
{

?>

<img src="<?php echo $row['image']; ?>">

<?php

}

?>

<h3>

<?php echo htmlspecialchars($row['item_name']); ?>

</h3>

<p>

<strong>Category:</strong>

<?php echo htmlspecialchars($row['category']); ?>

</p>

<p>

<strong>Model:</strong>

<?php echo htmlspecialchars($row['model']); ?>

</p>

<p>

<strong>Color:</strong>

<?php echo htmlspecialchars($row['color']); ?>

</p>

<p>

<strong>Location:</strong>

<?php echo htmlspecialchars($row['location']); ?>

</p>

<p>

<strong>Date:</strong>

<?php echo htmlspecialchars($row['item_date']); ?>

</p>

<a class="view-btn"

href="item-details.php?item_id=<?php echo $row['item_id']; ?>">
<p>
    <strong>Match Score:</strong>
    <?php echo $row['match_score']; ?>%
</p>
View Item

</a>

</div>

<?php

}

?>

</div>

<?php

}
else
{

?>

<h3>

No matching items found.

</h3>

<p>

Try checking again later. Someone may report your item in the future.

</p>

<?php

}

?>

</div>

</body>

</html>