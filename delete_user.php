<?php
include 'includes/connection.php';

// Check if an ID was passed in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Execute the delete query
    $sql = "DELETE FROM users WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Success - redirect back to users page
        header("Location: admin_users.php");
        exit();
    } else {
        // Handle error (optional: you could redirect with an error message in the URL)
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // No ID provided, send them back
    header("Location: admin_users.php");
    exit();
}
?>