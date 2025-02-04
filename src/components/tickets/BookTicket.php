<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');

    // Check if user is admin
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $userId = $_SESSION['user_id'];
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Fetch stations list
    $stationsQuery = "SELECT * FROM stations ORDER BY station_name ASC";
    $stationsResult = mysqli_query($connection, $stationsQuery);
    $stations = mysqli_fetch_all($stationsResult, MYSQLI_ASSOC);

    // Fetch trains list
    $trainsQuery = "SELECT * FROM trains ORDER BY train_name ASC";
    $trainsResult = mysqli_query($connection, $trainsQuery);
    $trains = mysqli_fetch_all($trainsResult, MYSQLI_ASSOC);

    // Fetch users list for admin
    if ($isAdmin) {
        $usersQuery = "SELECT id, email FROM users ORDER BY email ASC";
        $usersResult = mysqli_query($connection, $usersQuery);
        $users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = [];
        $success = "";

        // Validate required fields
        if (empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['seat_number'])) {
            $errors[] = "All fields are required!";
        } else {
            // For admin users, use posted user_id; otherwise use session user_id
            $user_id = $isAdmin ? filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT) : $_SESSION['user_id'];
            $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
            $start_station = filter_input(INPUT_POST, 'start_station', FILTER_VALIDATE_INT);
            $end_station = filter_input(INPUT_POST, 'end_station', FILTER_VALIDATE_INT);
            // Trim and convert seat number to uppercase to avoid mismatches
            $seat_number = strtoupper(trim(filter_input(INPUT_POST, 'seat_number', FILTER_SANITIZE_SPECIAL_CHARS)));

            // Check that start and end stations are not the same
            if ($start_station === $end_station) {
                $errors[] = "Start station and End station cannot be the same!";
            } else {
                // Fetch seat price from seats table and check availability
                $seatQuery = "SELECT base_price, is_booked FROM seats WHERE train_id = $train_id AND seat_number = '$seat_number'";
                $seatResult = mysqli_query($connection, $seatQuery);
                if (!$seatResult || mysqli_num_rows($seatResult) == 0) {
                    $errors[] = "Invalid seat! No seat found for train ID $train_id and seat number '$seat_number'.";
                } else {
                    $seat = mysqli_fetch_assoc($seatResult);
                    if ($seat['is_booked']) {
                        $errors[] = "This seat is already booked. Please choose another.";
                    }
                }

                // (Optional) You could have a route query here to factor in distance.
                // Since your tickets table does not have a distance field, we use the seat's base_price.

                if (empty($errors)) {
                    // Use the base_price as ticket price (you can modify the formula as needed)
                    $price = $seat['base_price'];

                    // Get status from the form (hidden input) or default to "confirmed"
                    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'confirmed';
                    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);

                    // Auto-generate a transaction id
                    $transaction_id = uniqid('txn_', true);

                    // Insert ticket into database. Note: booking_date and updated_at are auto-handled.
                    $sql = "INSERT INTO tickets (user_id, train_id, source_id, destination_id, seat_number, ticket_price, status, payment_method, transaction_id) 
                            VALUES ('$user_id', '$train_id', '$start_station', '$end_station', '$seat_number', '$price', '$status', '$payment_method', '$transaction_id')";
                    if (mysqli_query($connection, $sql)) {
                        // Optionally mark the seat as booked in the seats table
                        $updateSeatQuery = "UPDATE seats SET is_booked = 1 WHERE train_id = $train_id AND seat_number = '$seat_number'";
                        mysqli_query($connection, $updateSeatQuery);

                        $success = "Ticket booked successfully!";
                    } else {
                        $errors[] = "Error: " . mysqli_error($connection);
                    }
                }
            }
        }
    }
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <!-- Book Ticket Form -->
        <div class="w-full max-w-[410px]  bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Book Ticket</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <?php if ($isAdmin): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User Email</label>
                        <select name="user_id" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['email']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <input type="text" name="seat_number" placeholder="Seat Number"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>
                <!-- Hidden field for status -->
                <input type="hidden" name="status" value="confirmed">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <option value="Cash">Cash</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                    </select>
                </div>
                <!-- Transaction ID is auto-generated, so no form field is needed -->
                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Submit
                </button>
            </form>

            <!-- Display Success/Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="mt-4 text-red-500">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="mt-4 text-green-500">
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ticket List -->
        <div class="flex flex-1 justify-center items-center px-4 sticky top-0 ">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8 overflow-x-scroll">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Tickets</h2>
                <div class=" max-h-96">
                    <table class="min-w-full bg-white">
                        <thead class="sticky top-0 bg-gray-100">
                            <tr class="">
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">User ID</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Start Station</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">End Station</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Seat Number</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Price</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Payment Method</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            if ($isAdmin) {
                                $sql = "SELECT t.ticket_id, t.user_id, tr.train_name, s1.station_name AS start_station_name, s2.station_name AS end_station_name, t.seat_number, t.ticket_price, t.status, t.payment_method, t.transaction_id 
                                        FROM tickets t
                                        JOIN trains tr ON t.train_id = tr.train_id
                                        JOIN stations s1 ON t.source_id = s1.station_id
                                        JOIN stations s2 ON t.destination_id = s2.station_id";
                            } else {
                                $sql = "SELECT t.ticket_id, t.user_id, tr.train_name, s1.station_name AS start_station_name, s2.station_name AS end_station_name, t.seat_number, t.ticket_price, t.status, t.payment_method, t.transaction_id 
                                        FROM tickets t
                                        JOIN trains tr ON t.train_id = tr.train_id
                                        JOIN stations s1 ON t.source_id = s1.station_id
                                        JOIN stations s2 ON t.destination_id = s2.station_id
                                        WHERE t.user_id = $userId";
                            }
                            $result = mysqli_query($connection, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['user_id']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_name']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['start_station_name']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['end_station_name']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['seat_number']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['ticket_price']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200 capitalize'>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['payment_method']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['transaction_id']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='py-2 px-4 border-b border-gray-200 text-center'>No tickets found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>