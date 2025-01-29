<div class="flex">

    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.html');
    include('../../config/database.php');
    ?>

    <!-- Todo Post User Data -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            $username = "* Username is required!";
            $email = "* email is required!";
            $password = "* password is required!";
        } else {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password) VALUES ('$username', '$email', '$hash')";
            try {
                $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                mysqli_query($connection, $sql);
                echo "New record created successfully";
            } catch (mysqli_sql_exception $error) {
                echo $error . "Could Not connect";
            }
        }
    }
    ?>

    <div class="w-[80%] bg-gray-200  h-screen">
        <div class="flex gap-8 m-auto justify-center items-center h-full">
            <div class="w-1/2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">User Registration</h2>
                <form class="space-y-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-gray-700">Username</label>
                        <input type="text" name="username" placeholder="Name" pattern="[A-Za-z]+"
                            class="<?php echo $username ? " border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" placeholder="Email"
                            class="<?php echo $email ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Password</label>
                        <input type="password" name="password" placeholder="Password"
                            class="<?php echo $password ? "border-red-500" : "border border-black" ?> relative
                             w-full border rounded-md p-2 outline-none">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Sign Up
                    </button>
                </form>

            </div>
            <div class="w-1/2">
                <img src="train/assets/images/train.jpg" alt="Train" class="w-full h-full object-cover rounded-lg shadow-lg">
            </div>
        </div>
    </div>
</div>