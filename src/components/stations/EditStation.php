<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');
    // Create a database connection
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Edit Station</h2>
            <?php
            if (isset($_GET['id'])) {
                $station_id = $_GET['id'];
                $sql = "SELECT * FROM stations WHERE station_id = $station_id";
                $result = mysqli_query($connection, $sql);
                if ($row = mysqli_fetch_assoc($result)) {
                    $station_code = $row['station_code'];
                    $station_name = $row['station_name'];
                    $address = $row['address'];
                    $city = $row['city'];
                } else {
                    echo "<p class='text-red-500 text-center'>Station not found</p>";
                }
            }

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
                    $sql = "UPDATE stations SET station_code = '$station_code', station_name = '$station_name', address = '$address', city = '$city' WHERE station_id = $station_id";
                    try {
                        mysqli_query($connection, $sql);
                        $success = "Station updated successfully!";
                        echo "<input type='hidden' id='successMessage' value='$success'>";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            ?>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?id=$station_id"; ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station Code</label>
                    <input type="text" name="station_code" value="<?php echo isset($station_code) ? htmlspecialchars($station_code) : ''; ?>" placeholder="Enter station code"
                        class="<?php echo isset($errors['station_code']) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($errors['station_code'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['station_code']}</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Station Name</label>
                    <input type="text" name="station_name" value="<?php echo isset($station_name) ? htmlspecialchars($station_name) : ''; ?>" placeholder="Enter station name"
                        class="<?php echo isset($errors['station_name']) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($errors['station_name'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['station_name']}</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>" placeholder="Enter address"
                        class="<?php echo isset($errors['address']) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($errors['address'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['address']}</p>"; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="<?php echo isset($city) ? htmlspecialchars($city) : ''; ?>" placeholder="Enter city"
                        class="<?php echo isset($errors['city']) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($errors['city'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['city']}</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Update Station
                </button>
            </form>
        </div>

    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(function() {
                window.location.href = '/train/src/components/stations/AddStations.php'; // Redirect to the previous page
            }, 3000); // 3 seconds delay
        }
    });
</script>