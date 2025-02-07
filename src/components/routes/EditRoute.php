<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        // Redirect to a different page or show an error message
        header("Location: /path/to/your/error/page.php");
        exit();
    }

    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    $train_sql = "SELECT train_id, train_name FROM trains WHERE status = 'active'";
    $train_result = mysqli_query($connection, $train_sql);

    // Fetch station names and IDs
    $station_sql = "SELECT station_id, station_name FROM stations";
    $station_result = mysqli_query($connection, $station_sql);

    // Fetch route details if editing
    $route_id = isset($_GET['id']) ? $_GET['id'] : null;
    if ($route_id) {
        $route_sql = "SELECT * FROM routes WHERE route_id = '$route_id'";
        $route_result = mysqli_query($connection, $route_sql);
        $route = mysqli_fetch_assoc($route_result);
    }
    ?>
    <!-- Main Content Wrapper -->
    <div class="flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center"><?php echo $route_id ? 'Edit Route' : 'Add Route'; ?></h2>
            <form id="routeForm" class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . ($route_id ? "?id=$route_id" : ''); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train Name</label>
                    <select name="train_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select Train</option>
                        <?php
                        while ($train_row = mysqli_fetch_assoc($train_result)) {
                            $selected = $route && $route['train_id'] == $train_row['train_id'] ? 'selected' : '';
                            echo "<option value='" . $train_row['train_id'] . "' $selected>" . htmlspecialchars($train_row['train_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($train_id) && is_string($train_id)) echo "<p class='text-red-500 text-xs mt-1'>$train_id</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Station</label>
                    <select name="start_station" id="start_station" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select Start Station</option>
                        <?php
                        while ($station_row = mysqli_fetch_assoc($station_result)) {
                            $selected = $route && $route['source_id'] == $station_row['station_id'] ? 'selected' : '';
                            echo "<option value='" . $station_row['station_id'] . "' $selected>" . htmlspecialchars($station_row['station_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($start_station) && is_string($start_station)) echo "<p class='text-red-500 text-xs mt-1'>$start_station</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Station</label>
                    <select name="end_station" id="end_station" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select End Station</option>
                        <?php
                        // Reset MySQL result pointer to fetch stations again
                        mysqli_data_seek($station_result, 0);

                        while ($station_row = mysqli_fetch_assoc($station_result)) {
                            // Skip the selected start station
                            if (isset($_POST['start_station']) && $_POST['start_station'] == $station_row['station_id']) {
                                continue;
                            }
                            $selected = $route && $route['destination_id'] == $station_row['station_id'] ? 'selected' : '';
                            echo "<option value='" . $station_row['station_id'] . "' $selected>" . htmlspecialchars($station_row['station_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($end_station) && is_string($end_station)) echo "<p class='text-red-500 text-xs mt-1'>$end_station</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance (km)</label>
                    <input type="number" name="distance_km" placeholder="Enter Distance (km)"
                        value="<?php echo $route ? htmlspecialchars($route['distance']) : ''; ?>"
                        class="<?php echo isset($distance_km) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($distance_km) && is_string($distance_km)) echo "<p class='text-red-500 text-xs mt-1'>$distance_km</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    <?php echo $route_id ? 'Update Route' : 'Add Route'; ?>
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['distance_km'])) {
                    $train_id = "Train ID is required";
                    $start_station = "Start Station ID is required";
                    $end_station = "End Station ID is required";
                    $distance_km = "Distance is required";
                    $err = "Please fill all the fields";
                } else {
                    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_SANITIZE_NUMBER_INT);
                    $start_station = filter_input(INPUT_POST, 'start_station', FILTER_SANITIZE_NUMBER_INT);
                    $end_station = filter_input(INPUT_POST, 'end_station', FILTER_SANITIZE_NUMBER_INT);
                    $distance_km = filter_input(INPUT_POST, 'distance_km', FILTER_SANITIZE_NUMBER_INT);

                    if ($route_id) {
                        $sql = "UPDATE routes SET train_id='$train_id', source_id='$start_station', destination_id='$end_station', distance='$distance_km' WHERE route_id='$route_id'";
                    } else {
                        $sql = "INSERT INTO routes (train_id, source_id, destination_id, distance) VALUES ('$train_id', '$start_station', '$end_station', '$distance_km')";
                    }

                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = $route_id ? "Route updated successfully!" : "Route added successfully!";
                        echo "<input type='hidden' id='successMessage' value='$success'>";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            include('../../../constant/alerts.php');
            ?>
        </div>
    </div>
</div>

<!-- TODO THIS ADD CONFIRMATION IF LEAVE WITHOUT -->
<script>
    let formModified = false;

    document.getElementById('routeForm').addEventListener('change', function() {
        formModified = true;
    });

    window.addEventListener('beforeunload', function(e) {
        if (formModified) {
            var confirmationMessage = 'Are you sure you want to leave this page? Changes you made may not be saved.';
            (e || window.event).returnValue = confirmationMessage; // Gecko + IE
            return confirmationMessage; // Webkit, Safari, Chrome
        }
    });

    document.getElementById('routeForm').addEventListener('submit', function() {
        formModified = false;
    });

    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(function() {
                window.location.href = '/train/src/components/routes/Routes.php'; // Redirect to the previous page
            }, 1000); // 3 seconds delay
        }
    });
</script>