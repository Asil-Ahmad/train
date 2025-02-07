<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php'); // Ensure this file defines $db_server, $db_user, $db_password, and $db_name

    // Check if user is admin
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $userId = $_SESSION['user_id'];
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Fetch trains list with available seats
    $trainsQuery = "SELECT train_id, train_name, available_seats FROM trains WHERE status = 'active' ORDER BY train_name ASC";
    $trainsResult = mysqli_query($connection, $trainsQuery);
    $trains = mysqli_fetch_all($trainsResult, MYSQLI_ASSOC);

    // Fetch users list for admin
    if ($isAdmin) {
        $usersQuery = "SELECT user_id, email FROM users ORDER BY email ASC";
        $usersResult = mysqli_query($connection, $usersQuery);
        $users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = [];
        $success = "";

        // Validate required fields
        if (empty($_POST['train_id']) || empty($_POST['start_station']) || empty($_POST['end_station']) || empty($_POST['number_of_seats'])) {
            $errors[] = "All fields are required!";
        } else {
            // For admin users, use posted user_id; otherwise use session user_id
            $user_id = $isAdmin ? filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT) : $_SESSION['user_id'];
            $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
            $start_station = filter_input(INPUT_POST, 'start_station', FILTER_VALIDATE_INT);
            $end_station = filter_input(INPUT_POST, 'end_station', FILTER_VALIDATE_INT);
            $number_of_seats = filter_input(INPUT_POST, 'number_of_seats', FILTER_VALIDATE_INT);

            // Check that start and end stations are not the same
            if ($start_station === $end_station) {
                $errors[] = "Start station and End station cannot be the same!";
            } else {
                // Check if the number of seats requested is greater than the available seats
                $availableSeatsQuery = "SELECT available_seats FROM trains WHERE train_id = ?";
                $stmt = mysqli_prepare($connection, $availableSeatsQuery);
                mysqli_stmt_bind_param($stmt, "i", $train_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $available_seats);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                $status = $number_of_seats > $available_seats ? 'waiting' : 'confirmed';

                // Calculate total price (assuming a fixed price per seat, e.g., $10)
                $price_per_seat = 10;
                $total_price = $number_of_seats * $price_per_seat;

                // Insert booking into the database
                $insertQuery = "INSERT INTO bookings (user_id, train_id, start_station_id, end_station_id, number_of_seats, total_price, status) 
                                              VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection, $insertQuery);
                mysqli_stmt_bind_param($stmt, "iiiiids", $user_id, $train_id, $start_station, $end_station, $number_of_seats, $total_price, $status);

                if (mysqli_stmt_execute($stmt)) {
                    $success = "Booking successfully created!";
                    if ($status === 'confirmed') {
                        $sql = "UPDATE trains SET available_seats = available_seats - $number_of_seats WHERE train_id = $train_id";
                        $connection->query($sql);
                    }
                } else {
                    $errors[] = "Error creating booking: " . mysqli_error($connection);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Fetch stations list based on selected train
    $stations = [];
    if (!empty($_POST['train_id'])) {
        $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
        $stationsQuery = "SELECT DISTINCT s.station_id, s.station_name 
                          FROM routes r 
                          JOIN stations s ON r.station_id = s.station_id 
                          WHERE r.train_id = ? 
                          ORDER BY s.station_name ASC";
        $stmt = mysqli_prepare($connection, $stationsQuery);
        mysqli_stmt_bind_param($stmt, "i", $train_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stations = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    }
    ?>

    <!-- Ticket Preview Modal -->
    <div id="ticketPreviewModal" class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-2xl p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">E-Ticket</h2>
                <img src="/train/assets/Indian_Railways.png" alt="Logo" class="h-8">
            </div>
            
            <div id="ticketDetails" class="relative">
                <!-- Decorative elements -->
                <div class="absolute -left-8 top-1/2 transform -translate-y-1/2 h-8 w-8 bg-gray-200 rounded-full"></div>
                <div class="absolute -right-8 top-1/2 transform -translate-y-1/2 h-8 w-8 bg-gray-200 rounded-full"></div>
                <div class="border-dashed border-2 border-gray-200 rounded-xl p-6 bg-white">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                            <div class="text-xs text-gray-500">Booking ID</div>
                            <div class="text-sm font-semibold">#${ticket.booking_id}</div>
                        </div>
                        
                        <div class="flex justify-between items-start">
                            <div class="text-left">
                                <div class="text-xs text-gray-500">From</div>
                                <div class="text-sm font-semibold">${ticket.start_station_name}</div>
                            </div>
                            <div class="flex-1 px-4 pt-2">
                                <div class="relative h-0.5 bg-blue-500">
                                    <div class="absolute -top-2 left-0 w-4 h-4 rounded-full bg-blue-500"></div>
                                    <div class="absolute -top-2 right-0 w-4 h-4 rounded-full bg-blue-500"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">To</div>
                                <div class="text-sm font-semibold">${ticket.end_station_name}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-4">
                            <div>
                                <div class="text-xs text-gray-500">Train</div>
                                <div class="text-sm font-semibold">${ticket.train_name}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Seats</div>
                                <div class="text-sm font-semibold">${ticket.number_of_seats}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="text-sm font-semibold ${ticket.status === 'confirmed' ? 'text-green-500' : 'text-red-500'}">${ticket.status.toUpperCase()}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Price</div>
                                <div class="text-sm font-semibold">$${ticket.total_price}</div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500">Passenger</div>
                            <div class="text-sm font-semibold">${ticket.email}</div>
                            <div class="text-xs text-gray-500 mt-2">Booked On</div>
                            <div class="text-sm font-semibold">${ticket.booking_date}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button onclick="printTicket()" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button onclick="closeTicketPreview()" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function showTicketPreview(ticket) {
            const modal = document.getElementById('ticketPreviewModal');
            const ticketDetails = document.getElementById('ticketDetails');
            ticketDetails.querySelector('.border-dashed').innerHTML = `
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                        <div class="text-xs text-gray-500">Booking ID</div>
                        <div class="text-sm font-semibold">#${ticket.booking_id}</div>
                    </div>
                    
                    <div class="flex justify-between items-start">
                        <div class="text-left">
                            <div class="text-xs text-gray-500">From</div>
                            <div class="text-sm font-semibold">${ticket.start_station_name}</div>
                        </div>
                        <div class="flex-1 px-4 pt-2">
                            <div class="relative h-0.5 bg-blue-500">
                                <div class="absolute -top-2 left-0 w-4 h-4 rounded-full bg-blue-500"></div>
                                <div class="absolute -top-2 right-0 w-4 h-4 rounded-full bg-blue-500"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">To</div>
                            <div class="text-sm font-semibold">${ticket.end_station_name}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-4">
                        <div>
                            <div class="text-xs text-gray-500">Train</div>
                            <div class="text-sm font-semibold">${ticket.train_name}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Seats</div>
                            <div class="text-sm font-semibold">${ticket.number_of_seats}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Status</div>
                            <div class="text-sm font-semibold ${ticket.status === 'confirmed' ? 'text-green-500' : 'text-red-500'}">${ticket.status.toUpperCase()}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Price</div>
                            <div class="text-sm font-semibold">$${ticket.total_price}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="text-xs text-gray-500">Passenger</div>
                        <div class="text-sm font-semibold">${ticket.email}</div>
                        <div class="text-xs text-gray-500 mt-2">Booked On</div>
                        <div class="text-sm font-semibold">${ticket.booking_date}</div>
                    </div>
                </div>
            `;
            modal.classList.remove('hidden');
        }

        function closeTicketPreview() {
            document.getElementById('ticketPreviewModal').classList.add('hidden');
        }

        function printTicket() {
            const printContents = document.getElementById('ticketDetails').innerHTML;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            closeTicketPreview();
        }
    </script>

    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <!-- Book Ticket Form -->
        <div class="w-full max-w-[410px] bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Book Ticket</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <?php if ($isAdmin): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User Email</label>
                        <select name="user_id" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['email']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train</label>
                    <select name="train_id" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all" onchange="this.form.submit()">
                        <option value="">Select Train</option>
                        <?php foreach ($trains as $train): ?>
                            <option value="<?php echo $train['train_id']; ?>" <?php echo isset($_POST['train_id']) && $_POST['train_id'] == $train['train_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($train['train_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Station</label>
                    <select name="start_station" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all" onchange="this.form.submit()">
                        <option value="">Select Start Station</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?php echo $station['station_id']; ?>" <?php echo isset($_POST['start_station']) && $_POST['start_station'] == $station['station_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($station['station_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Station</label>
                    <select name="end_station" class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <option value="">Select End Station</option>
                        <?php foreach ($stations as $station): ?>
                            <?php if (!isset($_POST['start_station']) || $_POST['start_station'] != $station['station_id']): ?>
                                <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Seats</label>
                    <input type="number" name="number_of_seats" placeholder="Number of Seats"
                        class="border-gray-300 w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                </div>
                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Submit
                </button>
            </form>

            <!-- Display Success/Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="mt-4 text-red-500">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="mt-4 text-green-500">
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Booking List -->
        <div class="flex flex-1 justify-center items-center px-4 sticky top-0 ">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8 overflow-x-scroll">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Bookings</h2>
                <div class="">
                    <table class="min-w-full bg-white">
                        <thead class="sticky top-0 bg-gray-100">
                            <tr class="truncate">
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">User ID</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Start Station</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">End Station</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Seats</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Total Price</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Booking Date</th>
                                <th class="py-2  px-4 border-b-2 border-gray-200 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm gap-2">
                            <?php
                            if ($isAdmin) {
                                $sql = "SELECT b.booking_id, u.email, b.user_id, tr.train_name, s1.station_name AS start_station_name, s2.station_name AS end_station_name, b.number_of_seats, b.total_price, b.booking_date, b.status 
                                                         FROM bookings b
                                                         JOIN trains tr ON b.train_id = tr.train_id
                                                         JOIN users u ON b.user_id = u.user_id
                                                         JOIN stations s1 ON b.start_station_id = s1.station_id
                                                         JOIN stations s2 ON b.end_station_id = s2.station_id";
                            } else {
                                $sql = "SELECT b.booking_id,u.email, b.user_id, tr.train_name, s1.station_name AS start_station_name, s2.station_name AS end_station_name, b.number_of_seats, b.total_price, b.booking_date, b.status 
                                                         FROM bookings b
                                                         JOIN trains tr ON b.train_id = tr.train_id
                                                        JOIN users u ON b.user_id = u.user_id
                                                         JOIN stations s1 ON b.start_station_id = s1.station_id
                                                         JOIN stations s2 ON b.end_station_id = s2.station_id
                                                         WHERE b.user_id = $userId";
                            }
                            $result = mysqli_query($connection, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200 max-w-6'>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_name']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['start_station_name']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['end_station_name']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['number_of_seats']) . "</td>";
                                    $statusClass = $row['status'] === 'confirmed' ? 'bg-green-100 text-green-600 ' : 'bg-red-100 text-black/50 ';
                                    echo "<td class='py-2 truncate px-4 border-b text-center text-[10px] font-semibold uppercase border-gray-200 $statusClass'>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['total_price']) . "</td>";
                                    echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['booking_date']) . "</td>";
                                    echo "<td onclick='showTicketPreview(" . json_encode($row) . ")' class='py-2 cursor-pointer bg-green-600  text-center text-[10px] rounded-lg text-white truncate px-4 border-b border-gray-200'>Print Ticket</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='py-2 px-4 border-b border-gray-200 text-center'>No bookings found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
