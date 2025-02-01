
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station Name</label>
                    <input type="text" name="station_name" placeholder="Enter station name"
                        class="<?php echo isset($station_name) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($station_name) && is_string($station_name)) echo "<p class='text-red-500 text-xs mt-1'>$station_name</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Add Station
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['station_name'])) {
                    $station_name = "Station name is required";
                } else {
                    $station_name = filter_input(INPUT_POST, 'station_name', FILTER_SANITIZE_SPECIAL_CHARS);

                    $sql = "INSERT INTO stations (station_name) VALUES ('$station_name')";
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
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Station Name</th>
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
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['station_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'><a href='DeleteStation.php?id=" . $row['station_id'] . "' class='text-red-500 hover:text-red-700'>Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' class='py-2 px-4 border-b border-gray-200 text-center'>No stations found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>