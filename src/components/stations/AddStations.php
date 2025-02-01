<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Add Station</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station Code</label>
                    <input type="text" name="station_code" placeholder="Enter station code"
                        class="<?php echo isset($station_code) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($station_code) && is_string($station_code)) echo "<p class='text-red-500 text-xs mt-1'>$station_code</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station Name</label>
                    <input type="text" name="station_name" placeholder="Enter station name"
                        class="<?php echo isset($station_name) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($station_name) && is_string($station_name)) echo "<p class='text-red-500 text-xs mt-1'>$station_name</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" placeholder="Enter address"
                        class="<?php echo isset($address) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($address) && is_string($address)) echo "<p class='text-red-500 text-xs mt-1'>$address</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" placeholder="Enter city"
                        class="<?php echo isset($city) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($city) && is_string($city)) echo "<p class='text-red-500 text-xs mt-1'>$city</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Add Station
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $errors = [];

                if (empty($_POST['station_code'])) {
                    $errors['station_code'] = "Station code is required";
                } else {
                    $station_code = filter_input(INPUT_POST, 'station_code', FILTER_SANITIZE_SPECIAL_CHARS);
                }

                if (empty($_POST['station_name'])) {
                    $errors['station_name'] = "Station name is required";
                } else {
                    $station_name = filter_input(INPUT_POST, 'station_name', FILTER_SANITIZE_SPECIAL_CHARS);
                }

                $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
                $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS);

                if (empty($errors)) {
                    $sql = "INSERT INTO stations (station_code, station_name, address, city) VALUES ('$station_code', '$station_name', '$address', '$city')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "Station added successfully!";
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
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Stations</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Station Code</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Station Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Address</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">City</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php
                        $sql = "SELECT * FROM stations";
                        $result = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['station_code']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['station_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['address']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['city']) . "</td>";
                                //* Add a delete button with a link to delete the train_id
                                echo "<td class='py-2 px-4 border-b border-gray-200 flex gap-2'>
                                <a href='EditStation.php?id=" . $row['station_id'] . "' class='bg-[#2E7D32] text-white hover:bg-green-700 px-2 py-0.5 font-medium'>Edit</a>
                                <a href='DeleteStation.php?id=" . $row['station_id'] . "' class='text-white bg-[#D32F2F] hover:bg-red-700 px-2 py-0.5 font-medium'>Delete</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='py-2 px-4 border-b border-gray-200 text-center'>No stations found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>