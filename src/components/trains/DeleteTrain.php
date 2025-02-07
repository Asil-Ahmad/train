<?php
// Include database connection file
include('../../../config/database.php');
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to a different page or show an error message
    header("Location: /path/to/your/error/page.php");
    exit();
}

// Create a database connection
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);

// Check the connection
if ($connection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $train_id = $_GET['id'];
} else {
    die("ERROR: Invalid train ID.");
}

$sql = "DELETE FROM trains WHERE train_id = ?";
if ($stmt = mysqli_prepare($connection, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $train_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "✅ Train deleted successfully.";
        header("Location: AddTrains.php?success=1");
        exit();
    } else {
        echo "❌ MySQL Error: " . mysqli_error($connection);
    }
} else {
    echo "❌ Statement Preparation Error: " . mysqli_error($connection);
}
