<div class="flex flex-col sm:min-h-screen h-full bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');

    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    // Fetch trains
    $train_query = "SELECT train_id, train_name FROM trains";
    $train_result = mysqli_query($connection, $train_query);

    // Fetch stations
    $station_query = "SELECT station_id, station_name FROM stations";
    $station_result = mysqli_query($connection, $station_query);

    // Fetch users
    $user_query = "SELECT id, email FROM users";
    $user_result = mysqli_query($connection, $user_query);
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex sm:flex-row flex-col sm:gap-0 gap-5 items-start sm:px-4 px-0">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Book Ticket</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Email</label>
                    <select name="user_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <?php while ($user = mysqli_fetch_assoc($user_result)) { ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['email']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train</label>
                    <select name="train_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <?php while ($train = mysqli_fetch_assoc($train_result)) { ?>
                            <option value="<?php echo $train['train_id']; ?>"><?php echo htmlspecialchars($train['train_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Source Station</label>
                    <select name="source_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <?php while ($station = mysqli_fetch_assoc($station_result)) { ?>
                            <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destination Station</label>
                    <select name="destination_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <?php while ($station = mysqli_fetch_assoc($station_result)) { ?>
                            <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seat Number</label>
                    <input type="text" name="seat_number" placeholder="Enter seat number" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticket Price</label>
                    <input type="text" name="ticket_price" placeholder="Enter ticket price" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance</label>
                    <input type="number" name="distance" placeholder="Enter distance" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <input type="text" name="payment_method" placeholder="Enter payment method" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                    <input type="text" name="transaction_id" placeholder="Enter transaction ID" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <button type="submit" class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Book Ticket
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
                $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
                $source_id = filter_input(INPUT_POST, 'source_id', FILTER_VALIDATE_INT);
                $destination_id = filter_input(INPUT_POST, 'destination_id', FILTER_VALIDATE_INT);
                $seat_number = filter_input(INPUT_POST, 'seat_number', FILTER_SANITIZE_SPECIAL_CHARS);
                $ticket_price = filter_input(INPUT_POST, 'ticket_price', FILTER_VALIDATE_FLOAT);
                $distance = filter_input(INPUT_POST, 'distance', FILTER_VALIDATE_INT);
                $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);
                $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_SPECIAL_CHARS);

                if ($user_id && $train_id && $source_id && $destination_id && $seat_number && $ticket_price && $distance && $payment_method && $transaction_id) {
                    $sql = "INSERT INTO tickets (user_id, train_id, source_id, destination_id, seat_number, ticket_price, distance, payment_method, transaction_id) VALUES ('$user_id', '$train_id', '$source_id', '$destination_id', '$seat_number', '$ticket_price', '$distance', '$payment_method', '$transaction_id')";
                    try {
                        mysqli_query($connection, $sql);
                        $success = "Ticket booked successfully!";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                } else {
                    $err = "All fields are required.";
                }
            }
            include('../../../constant/alerts.php');
            ?>
        </div>
    </div>
</div>