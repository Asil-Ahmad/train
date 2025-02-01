<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.php');
    include('../../config/database.php');

    // Fetch train names and IDs
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    $train_sql = "SELECT train_id, train_name FROM trains";
    $train_result = mysqli_query($connection, $train_sql);

    // Fetch station names and IDs
    $station_sql = "SELECT station_id, station_name FROM stations";
    $station_result = mysqli_query($connection, $station_sql);
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Add Route</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train ID</label>
                    <select name="train_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select Train</option>
                        <?php
                        while ($train_row = mysqli_fetch_assoc($train_result)) {
                            echo "<option value='" . $train_row['train_id'] . "'>" . htmlspecialchars($train_row['train_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($train_id) && is_string($train_id)) echo "<p class='text-red-500 text-xs mt-1'>$train_id</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Station ID</label>
                    <select name="start_station" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select Start Station</option>
                        <?php
                        while ($station_row = mysqli_fetch_assoc($station_result)) {
                            echo "<option value='" . $station_row['station_id'] . "'>" . htmlspecialchars($station_row['station_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($start_station) && is_string($start_station)) echo "<p class='text-red-500 text-xs mt-1'>$start_station</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Station ID</label>
                    <select name="end_station" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select End Station</option>
                        <?php
                        mysqli_data_seek($station_result, 0); // Reset the result pointer to reuse the result set
                        while ($station_row = mysqli_fetch_assoc($station_result)) {
                            echo "<option value='" . $station_row['station_id'] . "'>" . htmlspecialchars($station_row['station_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($end_station) && is_string($end_station)) echo "<p class='text-red-500 text-xs mt-1'>$end_station</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance (km)</label>
                    <input type="number" name="distance_km" placeholder="Enter Distance (km)"
                        class="<?php echo isset($distance_km) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($distance_km) && is_string($distance_km)) echo "<p class='text-red-500 text-xs mt-1'>$distance_km</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Add Route
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['distance_km'])) {
                    $train_id = "Train ID is required";
                    $start_station = "Start Station ID is required";
                    $end_station = "End Station ID is required";
                    $distance_km = "Distance is required";
                } else {
                    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_SANITIZE_NUMBER_INT);
                    $start_station = filter_input(INPUT_POST, 'start_station', FILTER_SANITIZE_NUMBER_INT);
                    $end_station = filter_input(INPUT_POST, 'end_station', FILTER_SANITIZE_NUMBER_INT);
                    $distance_km = filter_input(INPUT_POST, 'distance_km', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                    $sql = "INSERT INTO routes (train_id, source_id, destination_id, distance) VALUES ('$train_id', '$start_station', '$end_station', '$distance_km')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "Route added successfully!";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            include('../../constant/alerts.php');
            ?>
        </div>
        <div class="flex flex-1 justify-center items-center px-4 ">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Routes</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Start Station</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">End Station</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Distance (km)</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php
                        $sql = "SELECT routes.*, trains.train_name, start_station.station_name AS start_station_name, end_station.station_name AS end_station_name
                                FROM routes
                                JOIN trains ON routes.train_id = trains.train_id
                                JOIN stations AS start_station ON routes.source_id = start_station.station_id
                                JOIN stations AS end_station ON routes.destination_id = end_station.station_id";
                        $result = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['start_station_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['end_station_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['distance']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'><a href='DeleteRoute.php?id=" . $row['route_id'] . "' class='text-red-500 hover:text-red-700'>Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='py-2 px-4 border-b border-gray-200 text-center'>No routes found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>