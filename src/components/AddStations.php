<div class="flex">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.html');
    include('../../config/database.php');
    ?>

    <div class="w-[80%] bg-gray-200  h-screen">
        <div class="flex gap-8 m-auto justify-center items-center h-full">
            <div class="w-1/2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Add Stations</h2>
                <form class="space-y-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-gray-700">Station Name</label>
                        <input type="text" name="station_name" placeholder="Station Name"
                            class="<?php echo $station_name ? " border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Location</label>
                        <input type="text" name="location" placeholder="Location"
                            class="<?php echo $location ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Submit
                    </button>
                </form>

                <!-- Todo Post User Data -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (empty($_POST['station_name']) || empty($_POST['location'])) {
                        $station_name = "* station Name is required!";
                        $location = "* location is required!";
                        // echo $location;
                    } else {
                        $station_name = filter_input(INPUT_POST, 'station_name', FILTER_SANITIZE_SPECIAL_CHARS);
                        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_SPECIAL_CHARS);


                        $sql = "INSERT INTO stations (station_name, location) VALUES ('$station_name', '$location')";
                        try {
                            $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                            mysqli_query($connection, $sql);
                            $success = "New record created successfully";
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