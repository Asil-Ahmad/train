<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex sm:flex-row flex-col sm:gap-0 gap-5 items-start sm:px-4 px-0">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Book Ticket</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                    <input type="number" name="user_id" placeholder="Enter user ID"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train ID</label>
                    <input type="number" name="train_id" placeholder="Enter train ID"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Source ID</label>
                    <input type="number" name="source_id" placeholder="Enter source ID"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destination ID</label>
                    <input type="number" name="destination_id" placeholder="Enter destination ID"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seat Number</label>
                    <input type="text" name="seat_number" placeholder="Enter seat number"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticket Price</label>
                    <input type="number" step="0.01" name="ticket_price" placeholder="Enter ticket price"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance</label>
                    <input type="number" name="distance" placeholder="Enter distance"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <option value="confirmed">Confirmed</option>
                        <option value="canceled">Canceled</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <input type="text" name="payment_method" placeholder="Enter payment method"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                    <input type="text" name="transaction_id" placeholder="Enter transaction ID"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Book Ticket
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['user_id']) || empty($_POST['train_id']) || empty($_POST['source_id']) || empty($_POST['destination_id']) || empty($_POST['seat_number']) || empty($_POST['ticket_price']) || empty($_POST['distance']) || empty($_POST['status']) || empty($_POST['payment_method']) || empty($_POST['transaction_id'])) {
                    echo "<p class='text-red-500 text-xs mt-1'>All fields are required</p>";
                } else {
                    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
                    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
                    $source_id = filter_input(INPUT_POST, 'source_id', FILTER_VALIDATE_INT);
                    $destination_id = filter_input(INPUT_POST, 'destination_id', FILTER_VALIDATE_INT);
                    $seat_number = filter_input(INPUT_POST, 'seat_number', FILTER_SANITIZE_SPECIAL_CHARS);
                    $ticket_price = filter_input(INPUT_POST, 'ticket_price', FILTER_VALIDATE_FLOAT);
                    $distance = filter_input(INPUT_POST, 'distance', FILTER_VALIDATE_INT);
                    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
                    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);
                    $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_SPECIAL_CHARS);

                    $sql = "INSERT INTO tickets (user_id, train_id, source_id, destination_id, seat_number, ticket_price, distance, status, payment_method, transaction_id) VALUES ('$user_id', '$train_id', '$source_id', '$destination_id', '$seat_number', '$ticket_price', '$distance', '$status', '$payment_method', '$transaction_id')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        echo "<p class='text-green-500 text-xs mt-1'>Ticket booked successfully!</p>";
                    } catch (mysqli_sql_exception $error) {
                        echo "<p class='text-red-500 text-xs mt-1'>" . $error->getMessage() . "</p>";
                    }
                }
            }
            ?>
        </div>
        <div class="flex overflow-x-scroll flex-1 justify-center items-center sm:px-4 px-0">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Tickets</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Ticket ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">User ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Source ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Destination ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Seat Number</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Ticket Price</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Distance</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Payment Method</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Transaction ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Booking Date</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php
                        $sql = "SELECT * FROM tickets";
                        $result = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['ticket_id']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['user_id']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_id']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['source_id']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['destination_id']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['seat_number']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['ticket_price']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['distance']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['status']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['payment_method']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['transaction_id']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['booking_date']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200 flex gap-2'>
                                <a href='EditTicket.php?id=" . $row['ticket_id'] . "' class='bg-[#2E7D32] text-white hover:bg-green-700 px-2 py-0.5 font-medium'>Edit</a>
                                <a href='DeleteTicket.php?id=" . $row['ticket_id'] . "' class='text-white bg-[#D32F2F] hover:bg-red-700 px-2 py-0.5 font-medium'>Delete</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='13' class='py-2 px-4 border-b border-gray-200 text-center'>No tickets found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>