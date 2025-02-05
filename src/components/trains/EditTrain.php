<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../../constant/header.html');
    include('../../../constant/sidebar.php');
    include('../../../config/database.php');

    // Establish database connection
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_GET['id'])) {
        $train_id = $_GET['id'];
        $sql = "SELECT * FROM trains WHERE train_id = $train_id";
        $result = mysqli_query($connection, $sql);
        $train = mysqli_fetch_assoc($result);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $train_number = filter_input(INPUT_POST, 'train_number', FILTER_SANITIZE_SPECIAL_CHARS);
        $train_name = filter_input(INPUT_POST, 'train_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $total_seats = filter_input(INPUT_POST, 'total_seats', FILTER_VALIDATE_INT);
        $available_seats = filter_input(INPUT_POST, 'available_seats', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($train_number) || empty($train_name) || empty($total_seats) || empty($available_seats) || empty($status)) {
            $error = "All fields are required";
        } elseif ($total_seats < $available_seats) {
            $error = "Available seats cannot be greater than total seats";
        } else {
            $sql = "UPDATE trains SET train_number='$train_number', train_name='$train_name', total_seats='$total_seats', available_seats='$available_seats', status='$status' WHERE train_id=$train_id";
            try {
                mysqli_query($connection, $sql);
                $success = "Train updated successfully!";
                echo "<input type='hidden' id='successMessage' value='$success'>";
            } catch (mysqli_sql_exception $error) {
                $err = $error->getMessage();
            }
        }
    }
    include('../../../constant/alerts.php');
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex sm:flex-row flex-col sm:gap-0 gap-5 items-center justify-center sm:px-4 px-0">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Edit Train</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?id=$train_id"; ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train Number</label>
                    <input type="text" name="train_number" value="<?php echo htmlspecialchars($train['train_number']); ?>" placeholder="Enter train number"
                        class="w-full px-4 py-3 rounded-lg border-gray-300 border-1 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train Name</label>
                    <input type="text" name="train_name" value="<?php echo htmlspecialchars($train['train_name']); ?>" placeholder="Enter train name"
                        class="w-full px-4 py-3 rounded-lg border-gray-300 border-1 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Seats</label>
                    <input type="number" name="total_seats" value="<?php echo htmlspecialchars($train['total_seats']); ?>" placeholder="Enter total seats"
                        class="w-full px-4 py-3 rounded-lg border-gray-300 border-1 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Available Seats</label>
                    <input type="number" name="available_seats" value="<?php echo htmlspecialchars($train['available_seats']); ?>" placeholder="Enter available seats"
                        class="w-full px-4 py-3 rounded-lg border-gray-300 border-1 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="radio" name="status" value="active" id="status_active" class="mr-2 accent-green-500" <?php echo ($train['status'] == 'active') ? 'checked' : ''; ?>>
                            <label for="status_active" class="text-sm text-gray-700">Active</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="status" value="inactive" id="status_inactive" class="mr-2 accent-red-500" <?php echo ($train['status'] == 'inactive') ? 'checked' : ''; ?>>
                            <label for="status_inactive" class="text-sm text-gray-700">Inactive</label>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Update Train
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(function() {
                window.location.href = '/train/src/components/trains/AddTrains.php'; // Redirect to the previous page
            }, 1000); // 3 seconds delay
        }
    });
</script>