<?php
session_start();
include('../../../constant/header.html');
include('../../../constant/sidebar.php');
include('../../../config/database.php');
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_seat'])) {
    $seat_id = filter_input(INPUT_POST, 'seat_id', FILTER_VALIDATE_INT);
    $train_id = filter_input(INPUT_POST, 'train_id', FILTER_VALIDATE_INT);
    $seat_number = filter_input(INPUT_POST, 'seat_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $base_price = filter_input(INPUT_POST, 'base_price', FILTER_VALIDATE_FLOAT);

    if ($seat_id && $train_id && $seat_number && $base_price) {
        $sql = "UPDATE seats SET train_id='$train_id', seat_number='$seat_number', base_price='$base_price', price='$base_price' WHERE seat_id='$seat_id'";
        try {
            mysqli_query($connection, $sql);
            $success = "Seat updated successfully!";
            header("Location: SeatAvailable.php?success=1");
            exit();
        } catch (mysqli_sql_exception $error) {
            $err = $error->getMessage();
        }
    } else {
        $err = "All fields are required";
    }
}
include('../../../constant/alerts.php');
if (isset($_GET['id'])) {
    $seat_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $sql = "SELECT * FROM seats WHERE seat_id='$seat_id'";
    $result = mysqli_query($connection, $sql);
    $seat = mysqli_fetch_assoc($result);
}
?>

<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <div class="flex sm:flex-row flex-col sm:gap-0 gap-5 items-center justify-center sm:px-4 px-0">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Edit Seat</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="seat_id" value="<?php echo htmlspecialchars($seat['seat_id']); ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train</label>
                    <select name="train_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                        <?php
                        $sql = "SELECT train_id, train_name FROM trains";
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $selected = $row['train_id'] == $seat['train_id'] ? 'selected' : '';
                            echo "<option value='" . $row['train_id'] . "' $selected>" . htmlspecialchars($row['train_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seat Number</label>
                    <input type="text" name="seat_number" value="<?php echo htmlspecialchars($seat['seat_number']); ?>"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
                    <input type="number" name="base_price" value="<?php echo htmlspecialchars($seat['base_price']); ?>"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                </div>

                <button type="submit" name="update_seat"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Update Seat
                </button>
            </form>
        </div>
    </div>
</div>