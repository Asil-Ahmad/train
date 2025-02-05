<?php
session_start();
include('../../../constant/header.html');
include('../../../constant/sidebar.php');
include('../../../config/database.php');
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);

if (isset($_GET['id'])) {
    $seat_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($seat_id) {
        $sql = "DELETE FROM seats WHERE seat_id = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $seat_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<p class='text-green-500 text-xs mt-1'>Seat deleted successfully!</p>";
            header("Location: SeatAvailable.php?success=1");
            exit();
        } else {
            echo "<p class='text-red-500 text-xs mt-1'>Error deleting seat: " . mysqli_error($connection) . "</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p class='text-red-500 text-xs mt-1'>Invalid seat ID</p>";
    }
} else {
    echo "<p class='text-red-500 text-xs mt-1'>No seat ID provided</p>";
}

mysqli_close($connection);
?>