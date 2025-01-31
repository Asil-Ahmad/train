<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.php');
    include('../../config/database.php');
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex items-start px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Add Train</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train Number</label>
                    <input type="text" name="train_number" placeholder="Enter train number"
                        class="<?php echo isset($train_number) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($train_number) && is_string($train_number)) echo "<p class='text-red-500 text-xs mt-1'>$train_number</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Train Name</label>
                    <input type="text" name="train_name" placeholder="Enter train name"
                        class="<?php echo isset($train_name) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($train_name) && is_string($train_name)) echo "<p class='text-red-500 text-xs mt-1'>$train_name</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Seats</label>
                    <input type="number" name="total_seats" placeholder="Enter total seats"
                        class="<?php echo isset($total_seats) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                    <?php if (isset($total_seats) && is_string($total_seats)) echo "<p class='text-red-500 text-xs mt-1'>$total_seats</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="radio" name="status" value="active" id="status_active" class="mr-2 accent-green-500 ">
                            <label for="status_active" class="text-sm text-gray-700">Active</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="status" value="inactive" id="status_inactive" class="mr-2 accent-red-500">
                            <label for="status_inactive" class="text-sm text-gray-700">Inactive</label>
                        </div>
                    </div>
                    <?php if (isset($status) && is_string($status)) echo "<p class='text-red-500 text-xs mt-1'>$status</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                    Add Train
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['train_number']) || empty($_POST['train_name']) || empty($_POST['total_seats']) || empty($_POST['status'])) {
                    $train_number = "Train number is required";
                    $train_name = "Train name is required";
                    $total_seats = "Total seats is required";
                    $status = "Status is required";
                } else {
                    $train_number = filter_input(INPUT_POST, 'train_number', FILTER_SANITIZE_SPECIAL_CHARS);
                    $train_name = filter_input(INPUT_POST, 'train_name', FILTER_SANITIZE_SPECIAL_CHARS);
                    $total_seats = filter_input(INPUT_POST, 'total_seats', FILTER_VALIDATE_INT);
                    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

                    $sql = "INSERT INTO trains (train_number, train_name, total_seats, status) VALUES ('$train_number', '$train_name', '$total_seats', '$status')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "Train added successfully!";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            ?>

            <?php if (isset($success)): ?>
                <div id="successToast" class="fixed top-4 right-4 p-4 bg-green-50 rounded-lg shadow-lg transition-opacity duration-500">
                    <p class="text-green-600 text-sm"><?php echo $success; ?></p>
                </div>
                <script>
                    setTimeout(() => {
                        document.getElementById('successToast').style.opacity = '0';
                        setTimeout(() => {
                            document.getElementById('successToast').style.display = 'none';
                        }, 500);
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if (isset($err)): ?>
                <div id="errorToast" class="fixed top-4 right-4 p-4 bg-red-50 rounded-lg shadow-lg transition-opacity duration-500">
                    <p class="text-red-600 text-sm"><?php echo $err; ?></p>
                </div>
                <script>
                    setTimeout(() => {
                        document.getElementById('errorToast').style.opacity = '0';
                        setTimeout(() => {
                            document.getElementById('errorToast').style.display = 'none';
                        }, 500);
                    }, 8000);
                </script>
            <?php endif; ?>
        </div>
        <div class="flex flex-1 justify-center items-center px-4 ">
            <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">List of Trains</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train Number</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Total Seats</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM trains";
                        $result = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_number']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_name']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['total_seats']) . "</td>";
                                echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='py-2 px-4 border-b border-gray-200 text-center'>No trains found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- List of Trains -->