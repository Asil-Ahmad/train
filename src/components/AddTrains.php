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
                <h2 class="text-2xl font-bold mb-6">Add Train</h2>
                <form class="space-y-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-gray-700">Train Name</label>
                        <input type="text" name="train_name" placeholder="Train Name"
                            class="<?php echo $train_name ? " border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Total Seats</label>
                        <input type="number" name="total_seats" placeholder="Total Seats"
                            class="<?php echo $total_seats ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Status</label>
                        <div class="flex items-center">
                            <input type="radio" name="status" value="active" id="status_active" class="mr-2">
                            <label for="status_active" class="mr-4">Active</label>
                            <input type="radio" name="status" value="inactive" id="status_inactive" class="mr-2">
                            <label for="status_inactive">Inactive</label>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Submit
                    </button>
                </form>

                <!-- Todo Post User Data -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (empty($_POST['train_name']) || empty($_POST['total_seats']) || empty($_POST['status'])) {
                        $train_name = "* Train Name is required!";
                        $total_seats = "* total_seats is required!";
                        $status = "* status is required!";
                    } else {
                        $train_name = filter_input(INPUT_POST, 'train_name', FILTER_SANITIZE_SPECIAL_CHARS);
                        $total_seats = filter_input(INPUT_POST, 'total_seats', FILTER_VALIDATE_INT);
                        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

                        $sql = "INSERT INTO trains (train_name, total_seats, status) VALUES ('$train_name', '$total_seats', '$status')";
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