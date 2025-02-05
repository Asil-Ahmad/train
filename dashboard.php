<div class="flex flex-col min-h-screen  bg-[#F5F5F5]">
    <?php
    session_start();
    include('./constant/header.html');
    include('./constant/sidebar.php');
    include('./config/database.php');
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);

    ?>

    <!-- Main Content Wrapper -->
    <div class="flex justify-center items-center sm:px-4 px-0 ">
        <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Available Trains</h2>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train Number</th>
                        <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Train Name</th>
                        <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Total Seats</th>
                        <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Available Seats</th>
                        <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-2 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Live</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php
                    $sql = "SELECT * FROM trains";
                    $result = mysqli_query($connection, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            if ($row['status'] != 'active') {
                                continue;
                            }
                            echo "<tr>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_number']) . "</td>";
                            echo "<td class='py-2 truncate px-4 border-b border-gray-200'>" . htmlspecialchars($row['train_name']) . "</td>";
                            echo "<td class='py-2 px-4 border-b text-center border-gray-200'>" . htmlspecialchars($row['total_seats']) . "</td>";
                            $available_seats = htmlspecialchars($row['available_seats']);
                            $bg_color = 'bg-green-500'; // Default color

                            if ($available_seats < 20) {
                                $bg_color = 'bg-red-600';
                            } elseif ($available_seats < 50) {
                                $bg_color = 'bg-yellow-600';
                            } elseif ($available_seats < 70) {
                                $bg_color = 'bg-orange-600';
                            } 
                            echo "<td class='py-2 $bg_color px-4 border-b border-gray-200 text-center text-white font-bold'>$available_seats</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'><span class='relative flex h-3 w-3'><span class='animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75'></span><span class='relative inline-flex rounded-full h-3 w-3 bg-green-500'></span></span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='py-2 px-4 border-b border-gray-200 text-center'>No trains found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>