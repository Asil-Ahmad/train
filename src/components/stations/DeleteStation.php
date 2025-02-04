<?php
// Include database connection file
include('../../../config/database.php');

// Create a database connection
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);

// Check the connection
if ($connection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $station_id = $_GET['id'];
} else {
    die("ERROR: Invalid station ID.");
}

$sql = "DELETE FROM stations WHERE station_id = ?";
if ($stmt = mysqli_prepare($connection, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $station_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "✅ Station deleted successfully.";
        header("Location: AddStations.php?success=1");
        exit();
    } else {
        echo "❌ MySQL Error: " . mysqli_error($connection);
    }
} else {
    echo "❌ Statement Preparation Error: " . mysqli_error($connection);
}

mysqli_close($connection);
?>
