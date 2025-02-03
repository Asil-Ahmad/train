<?php
session_start();
include('../../../config/database.php');

if (isset($_GET['id'])) {
    $route_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($route_id) {
        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);

        if ($connection) {
            $sql = "DELETE FROM routes WHERE route_id = ?";
            $stmt = mysqli_prepare($connection, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $route_id);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $_SESSION['success'] = "Route deleted successfully!";
                } else {
                    $_SESSION['error'] = "Failed to delete route. Route ID may not exist.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['error'] = "Failed to prepare the SQL statement.";
            }

            mysqli_close($connection);
        } else {
            $_SESSION['error'] = "Failed to connect to the database.";
        }
    } else {
        $_SESSION['error'] = "Invalid route ID.";
    }
} else {
    $_SESSION['error'] = "Route ID not provided.";
}

header("Location: /train/src/components/routes/Routes.php");
exit();
