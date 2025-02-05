<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');

    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    $train_sql = "SELECT train_id, train_name FROM trains WHERE status = 'active'";
    $train_result = mysqli_query($connection, $train_sql);

    // Fetch station names and IDs
    $station_sql = "SELECT station_id, station_name FROM stations";
    $station_result = mysqli_query($connection, $station_sql);
    ?>
    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <div class="w-full min-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Add Route</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train Name</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station</label>
                    <select name="station_id" id="station_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="">Select Station</option>
                        <?php
                        while ($station_row = mysqli_fetch_assoc($station_result)) {
                            echo "<option value='" . $station_row['station_id'] . "'>" . htmlspecialchars($station_row['station_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($station_id) && is_string($station_id)) echo "<p class='text-red-500 text-xs mt-1'>$station_id</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station Order</label>
                    <input type="number" name="station_order" placeholder="Enter Station Order"
                        class="<?php echo isset($station_order) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($station_order) && is_string($station_order)) echo "<p class='text-red-500 text-xs mt-1'>$station_order</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance from Previous Station (km)</label>
                    <input type="number" name="distance_from_previous_station" placeholder="Enter Distance (km)"
                        class="<?php echo isset($distance_from_previous_station) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($distance_from_previous_station) && is_string($distance_from_previous_station)) echo "<p class='text-red-500 text-xs mt-1'>$distance_from_previous_station</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Add Route
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['train_id']) || empty($_POST['station_id']) || empty($_POST['station_order']) || empty($_POST['distance_from_previous_station'])) {
                    $train_id = "Train ID is required";
                    $station_id = "Station ID is required";
                    $station_order = "Station Order is required";
                    $distance_from_previous_station = "Distance from Previous Station is required";
                    $err = "Please fill all the fields";
                } else {
                    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_SANITIZE_NUMBER_INT);
                    $station_id = filter_input(INPUT_POST, 'station_id', FILTER_SANITIZE_NUMBER_INT);
                    $station_order = filter_input(INPUT_POST, 'station_order', FILTER_SANITIZE_NUMBER_INT);
                    $distance_from_previous_station = filter_input(INPUT_POST, 'distance_from_previous_station', FILTER_SANITIZE_NUMBER_INT);

                    $sql = "INSERT INTO routes (train_id, station_id, station_order, distance_from_previous_station) VALUES ('$train_id', '$station_id', '$station_order', '$distance_from_previous_station')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "Route added successfully!";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            include('../../../constant/alerts.php');
            ?>
        </div>
        <div class="flex flex-1 justify-center items-center px-4 ">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Routes</h2>
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Train</label>
                        <select name="filter_train_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                            <option value="">Select Train</option>
                            <?php
                            mysqli_data_seek($train_result, 0); // Reset the train result pointer
                            while ($train_row = mysqli_fetch_assoc($train_result)) {
                                $selected = isset($_GET['filter_train_id']) && $_GET['filter_train_id'] == $train_row['train_id'] ? 'selected' : '';
                                echo "<option value='" . $train_row['train_id'] . "' $selected>" . htmlspecialchars($train_row['train_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                        Filter Routes
                    </button>
                </form>
                <table class="min-w-full bg-white mt-6">
                    <thead>
                        <tr class="truncate">
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider ">Train Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Station Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Station Order</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Distance(km)</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm ">
                        <?php
                        $filter_train_id = isset($_GET['filter_train_id']) ? filter_input(INPUT_GET, 'filter_train_id', FILTER_SANITIZE_NUMBER_INT) : '';
                        $sql = "SELECT routes.*, trains.train_name, stations.station_name
                                FROM routes
                                JOIN trains ON routes.train_id = trains.train_id
                                JOIN stations ON routes.station_id = stations.station_id";
                        if ($filter_train_id) {
                            $sql .= " WHERE routes.train_id = '$filter_train_id'";
                        }
                        $sql .= " ORDER BY routes.train_id, routes.station_order";
                        $result = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result) > 0 ) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='py-2 px-4 border-b border-gray-200 truncate '>" . htmlspecialchars($row['train_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200 truncate'>" . htmlspecialchars($row['station_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['station_order']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['distance_from_previous_station']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200 flex gap-2'>
                                <a href='EditRoute.php?id=" . $row['route_id'] . "' class='bg-[#2E7D32] text-white hover:bg-green-700 px-2 py-0.5 font-medium'>Edit</a>
                                <a href='DeleteRoute.php?id=" . $row['route_id'] . "' class='text-white bg-[#D32F2F] hover:bg-red-700 px-2 py-0.5 font-medium'>Delete</a>
                                </td>";
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
