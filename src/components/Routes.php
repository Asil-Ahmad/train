<div class="flex">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.html');
    include('../../config/database.php');
    ?>

    <div class="w-[80%] bg-gray-200 h-screen">
        <div class="flex gap-8 m-auto justify-center items-center h-full">
            <div class="w-1/2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Add Route</h2>
                <form class="space-y-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-gray-700">Train ID</label>
                        <input type="number" name="train_id" placeholder="Train ID"
                            class="<?php echo $train_id ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Start Station ID</label>
                        <input type="number" name="start_station" placeholder="Start Station ID"
                            class="<?php echo $start_station ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">End Station ID</label>
                        <input type="number" name="end_station" placeholder="End Station ID"
                            class="<?php echo $end_station ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Distance (km)</label>
                        <input type="text" name="distance_km" placeholder="Distance (km)"
                            class="<?php echo $distance_km ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Travel Time</label>
                        <input type="time" name="travel_time" placeholder="Travel Time"
                            class="<?php echo $travel_time ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Submit
                    </button>
                </form>

                <!-- Todo Post User Data -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['distance_km']) || empty($_POST['travel_time'])) {
                        $train_id = "* Train ID is required!";
                        $start_station = "* Start Station ID is required!";
                        $end_station = "* End Station ID is required!";
                        $distance_km = "* Distance is required!";
                        $travel_time = "* Travel Time is required!";
                    } else {
                        $train_id = filter_input(INPUT_POST, 'train_id', FILTER_SANITIZE_NUMBER_INT);
                        $start_station = filter_input(INPUT_POST, 'start_station', FILTER_SANITIZE_NUMBER_INT);
                        $end_station = filter_input(INPUT_POST, 'end_station', FILTER_SANITIZE_NUMBER_INT);
                        $distance_km = filter_input(INPUT_POST, 'distance_km', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        $travel_time = filter_input(INPUT_POST, 'travel_time', FILTER_SANITIZE_SPECIAL_CHARS);

                        $sql = "INSERT INTO routes (train_id, start_station, end_station, distance_km, travel_time) VALUES ('$train_id', '$start_station', '$end_station', '$distance_km', '$travel_time')";
                        try {
                            $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                            mysqli_query($connection, $sql);
                            $success = "New route added successfully";
                        } catch (mysqli_sql_exception $error) {
                            $err = $error;
                        }
                    }
                }
                ?>
                <small class="text-green-500"><?php echo $success ?></small>
                <small class="text-red-500"><?php echo $err ?></small>
            </div>
        </div>
    </div>
</div>