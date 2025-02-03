<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');

    // Assuming you have a way to determine if the user is an admin
    $isAdmin = $_SESSION['user_role'] === 'admin';
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    // Fetch stations list
    $stationsQuery = "SELECT * FROM stations ORDER BY station_name ASC";
    $stationsResult = mysqli_query($connection, $stationsQuery);
    $stations = mysqli_fetch_all($stationsResult, MYSQLI_ASSOC);

    // Fetch trains list
    $trainsQuery = "SELECT * FROM trains ORDER BY train_name ASC";
    $trainsResult = mysqli_query($connection, $trainsQuery);
    $trains = mysqli_fetch_all($trainsResult, MYSQLI_ASSOC);
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Book Ticket</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <?php if ($isAdmin): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                        <input type="number" name="user_id" placeholder="User ID"
                            class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train</label>
                    <select name="train_id" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <option value="">Select Train</option>
                        <?php foreach ($trains as $train): ?>
                            <option value="<?php echo $train['train_id']; ?>"><?php echo htmlspecialchars($train['train_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Station</label>
                    <select name="start_station" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <option value="">Select Start Station</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Station</label>
                    <select name="end_station" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <option value="">Select End Station</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seat Number</label>
                    <input type="number" name="seat_number" placeholder="Seat Number"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="text" name="price" placeholder="Price"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance</label>
                    <input type="number" name="distance" placeholder="Distance"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <option value="confirmed">Confirmed</option>
                        <option value="canceled">Canceled</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <input type="text" name="payment_method" placeholder="Payment Method"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                    <input type="text" name="transaction_id" placeholder="Transaction ID"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Submit
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['seat_number']) || empty($_POST['price']) || empty($_POST['distance']) || empty($_POST['status']) || empty($_POST['payment_method']) || empty($_POST['transaction_id'])) {
                    $err = "All fields are required!";
                } else {
                    $user_id = $isAdmin ? filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT) : $_SESSION['user_id'];
                    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
                    $start_station = filter_input(INPUT_POST, 'start_station', FILTER_VALIDATE_INT);
                    $end_station = filter_input(INPUT_POST, 'end_station', FILTER_VALIDATE_INT);
                    $seat_number = filter_input(INPUT_POST, 'seat_number', FILTER_SANITIZE_SPECIAL_CHARS);
                    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
                    $distance = filter_input(INPUT_POST, 'distance', FILTER_VALIDATE_INT);
                    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
                    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);
                    $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_SPECIAL_CHARS);

                    $sql = "INSERT INTO tickets (user_id, train_id, source_id, destination_id, seat_number, ticket_price, distance, status, payment_method, transaction_id) VALUES ('$user_id', '$train_id', '$start_station', '$end_station', '$seat_number', '$price', '$distance', '$status', '$payment_method', '$transaction_id')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "New ticket booked successfully";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            ?>
            <small class="text-green-500"><?php echo $success ?? ''; ?></small>
            <small class="text-red-500"><?php echo $err ?? ''; ?></small>
        </div>
        <div class="flex flex-1 justify-center items-center px-4 sticky top-5">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Tickets</h2>
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full bg-white">
                        <thead class="sticky top-0 bg-gray-100">
                            <tr>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">User ID</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train ID</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Start Station</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">End Station</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Seat Number</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Price</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Distance</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Payment Method</th>
                                <th class="py-2 px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            $sql = "SELECT * FROM tickets";
                            $result = mysqli_query($connection, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['user_id']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_id']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['source_id']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['destination_id']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['seat_number']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['ticket_price']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['distance']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200 capitalize'>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['payment_method']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['transaction_id']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' class='py-2 px-4 border-b border-gray-200 text-center'>No tickets found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>