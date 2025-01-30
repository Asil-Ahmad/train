<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.html');
    include('../../config/database.php');
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex flex-1 justify-center items-center px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">Create Account</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name"
                        class="<?php echo isset($name) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border  focus:border-blue-500 transition-all">
                    <?php if(isset($name) && is_string($name)) echo "<p class='text-red-500 text-xs mt-1'>$name</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com"
                        class="<?php echo isset($email) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border  focus:border-blue-500 transition-all">
                    <?php if(isset($email) && is_string($email)) echo "<p class='text-red-500 text-xs mt-1'>$email</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" placeholder="Enter your phone number"
                        class="<?php echo isset($phone) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border  focus:border-blue-500 transition-all">
                    <?php if(isset($phone) && is_string($phone)) echo "<p class='text-red-500 text-xs mt-1'>$phone</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••"
                        class="<?php echo isset($password) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border  focus:border-blue-500 transition-all">
                    <?php if(isset($password) && is_string($password)) echo "<p class='text-red-500 text-xs mt-1'>$password</p>"; ?>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none  focus:ring-offset-2 transition-all">
                    Create Account
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['password'])) {
                    $name = empty($_POST['name']) ? "Name is required" : "";
                    $email = empty($_POST['email']) ? "Email is required" : "";
                    $phone = empty($_POST['phone']) ? "Phone is required" : "";
                    $password = empty($_POST['password']) ? "Password is required" : "";
                } else {
                    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
                    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
                    $created_at = date('Y-m-d H:i:s');

                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (name, email, phone, password, created_at) 
                           VALUES ('$name', '$email', '$phone', '$hash', '$created_at')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "Account created successfully!";
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            ?>
            <?php if(isset($success)): ?>
                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                    <p class="text-green-600 text-sm"><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
            <?php if(isset($err)): ?>
                <div class="mt-4 p-4 bg-red-50 rounded-lg">
                    <p class="text-red-600 text-sm"><?php echo $err; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
