<?php
session_start();
include('../../../constant/header.html');
include('../../../constant/sidebar.php');
include('../../../config/database.php');
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
?>

<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <!-- Main Content Wrapper -->
    <div class="flex sm:flex-row flex-col sm:gap-0 gap-5 items-start sm:px-4 px-0">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Add Seat</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train</label>
                    <select name="train_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <?php
                        $sql = "SELECT train_id, train_name FROM trains";
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['train_id'] . "'>" . htmlspecialchars($row['train_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seat Number</label>
                    <input type="text" name="seat_number" placeholder="Enter seat number"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
                    <input type="number" name="base_price" placeholder="Enter base price"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Add Seat
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['train_id']) || empty($_POST['seat_number']) || empty($_POST['base_price'])) {
                    echo "<p class='text-red-500 text-xs mt-1'>All fields are required</p>";
                } else {
                    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
                    $seat_number = filter_input(INPUT_POST, 'seat_number', FILTER_SANITIZE_SPECIAL_CHARS);
                    $base_price = filter_input(INPUT_POST, 'base_price', FILTER_VALIDATE_FLOAT);

                    $sql = "INSERT INTO seats (train_id, seat_number, base_price, price) VALUES ('$train_id', '$seat_number', '$base_price', '$base_price')";
                    try {
                        mysqli_query($connection, $sql);
                        echo "<p class='text-green-500 text-xs mt-1'>Seat added successfully!</p>";
                    } catch (mysqli_sql_exception $error) {
                        echo "<p class='text-red-500 text-xs mt-1'>" . $error->getMessage() . "</p>";
                    }
                }
            }
            ?>
        </div>
        <div class="flex flex-1 justify-center items-center sm:px-4 px-0">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Seats</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Seat Number</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Base Price</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Is Booked</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php
                        $sql = "SELECT seats.*, trains.train_name FROM seats JOIN trains ON seats.train_id = trains.train_id";
                        $result = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['seat_number']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['base_price']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . ($row['is_booked'] ? 'Yes' : 'No') . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200 flex gap-2'>
                                <a href='EditSeat.php?id=" . $row['seat_id'] . "' class='bg-[#2E7D32] text-white hover:bg-green-700 px-2 py-0.5 font-medium'>Edit</a>
                                <a href='DeleteSeat.php?id=" . $row['seat_id'] . "' class='text-white bg-[#D32F2F] hover:bg-red-700 px-2 py-0.5 font-medium'>Delete</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='py-2 px-4 border-b border-gray-200 text-center'>No seats found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>