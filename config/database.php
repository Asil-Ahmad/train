<?php
$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "trainApp";
$connection = "";

try {
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
} catch (mysqli_sql_exception $error) {
    echo $error . "Could Not connect";
}
// if ($connection) {
//     echo "Connected successfully <br>";
// }
