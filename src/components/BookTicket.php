<div class="">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.php');
    include('../../config/database.php');
    ?>

    <div class=" bg-gray-200 h-screen">
        <div class="flex gap-8 m-auto justify-center items-center h-full">
            <div class="w-1/2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Book Ticket</h2>
                <form class="space-y-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-gray-700">User ID</label>
                        <input type="number" name="user_id" placeholder="User ID"
                            class="border border-black relative w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Train ID</label>
                        <input type="number" name="train_id" placeholder="Train ID"
                            class="border border-black relative w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Start Station</label>
                        <input type="number" name="start_station" placeholder="Start Station"
                            class="border border-black relative w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">End Station</label>
                        <input type="number" name="end_station" placeholder="End Station"
                            class="border border-black relative w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Seat Number</label>
                        <input type="number" name="seat_number" placeholder="Seat Number"
                            class="border border-black relative w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Price</label>
                        <input type="text" name="price" placeholder="Price"
                            class="border border-black relative w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Status</label>
                        <div class="flex items-center">
                            <input type="radio" name="status" value="booked" id="status_booked" class="mr-2">
                            <label for="status_booked" class="mr-4">Booked</label>
                            <input type="radio" name="status" value="cancelled" id="status_cancelled" class="mr-2">
                            <label for="status_cancelled">Cancelled</label>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Submit
                    </button>
                </form>

                <!-- Todo Post Ticket Data -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (empty($_POST['user_id']) || empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['seat_number']) || empty($_POST['price']) || empty($_POST['status'])) {
                        $err = "All fields are required!";
                    } else {
                        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
                        $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
                        $start_station = filter_input(INPUT_POST, 'start_station', FILTER_VALIDATE_INT);
                        $end_station = filter_input(INPUT_POST, 'end_station', FILTER_VALIDATE_INT);
                        $seat_number = filter_input(INPUT_POST, 'seat_number', FILTER_VALIDATE_INT);
                        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
                        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

                        $sql = "INSERT INTO tickets (user_id, train_id, start_station, end_station, seat_number, price, status) VALUES ('$user_id', '$train_id', '$start_station', '$end_station', '$seat_number', '$price', '$status')";
                        try {
                            $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                            mysqli_query($connection, $sql);
                            $success = "New ticket booked successfully";
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