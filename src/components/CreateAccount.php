<div class="flex flex-col min-h-screen bg-[#F5F5F5]">
    <?php
    session_start();
    include('../../constant/header.html');
    include('../../constant/sidebar.php');
    include('../../config/database.php');

    // Include PHPMailer files
    require '../PHPMailer.php';
    require '../SMTP.php';
    require '../Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;

    // Function to send email
    function sendEmail($email, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'asil.infoseek@gmail.com';
            $mail->Password = 'tkqmrgkufgvxrgfe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('asil.infoseek@gmail.com', 'Asil');
            $mail->addAddress($email);
            $mail->addAttachment(__DIR__ . '/../../assets/Indian_Railways.png');

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            $username = empty($_POST['username']) ? "Username is required" : "";
            $email = empty($_POST['email']) ? "Email is required" : "";
            $password = empty($_POST['password']) ? "Password is required" : "";
        } else {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;

            $subject = 'BookMyTrain';
            sendEmail($email, "Your OTP Code", "Your OTP code is $otp");
            $otp_sent = true;
        }
    }
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex flex-1 justify-center items-center px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Create Account</h2>
            <?php if (isset($otp_sent) && $otp_sent): ?>
                <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">OTP</label>
                        <input type="text" name="otp" placeholder="Enter OTP"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 transition-all">
                    </div>
                    <button type="submit" name="verify_otp"
                        class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                        Verify OTP
                    </button>
                </form>
                
            <?php else: ?>
                <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" placeholder="Enter your username"
                            class="<?php echo isset($username) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <?php if (isset($username) && is_string($username)) echo "<p class='text-red-500 text-xs mt-1'>$username</p>"; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="you@example.com"
                            class="<?php echo isset($email) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <?php if (isset($email) && is_string($email)) echo "<p class='text-red-500 text-xs mt-1'>$email</p>"; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" placeholder="••••••••"
                            class="<?php echo isset($password) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border focus:border-blue-500 transition-all">
                        <?php if (isset($password) && is_string($password)) echo "<p class='text-red-500 text-xs mt-1'>$password</p>"; ?>
                    </div>

                    <button type="submit" name="send_otp"
                        class="w-full bg-[#0055A5]  text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-offset-2 transition-all">
                        Send OTP
                    </button>
                </form>
            <?php endif; ?>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
                if ($_POST['otp'] == $_SESSION['otp']) {
                    $username = $_SESSION['username'];
                    $email = $_SESSION['email'];
                    $password = $_SESSION['password'];
                    $created_at = date('Y-m-d H:i:s');

                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (username, email, password) 
                           VALUES ('$username', '$email', '$hash')";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        mysqli_query($connection, $sql);
                        $success = "Account created successfully!";
                        unset($_SESSION['otp'], $_SESSION['username'], $_SESSION['email'], $_SESSION['password']);
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                } else {
                    $err = "Invalid OTP. Please try again.";
                }
            }
            ?>
            <!-- Todo Toast Notification -->
            <?php if (isset($success)): ?>
                <?php include('../../constant/alerts.php'); ?>
            <?php endif; ?>
            <?php if (isset($err)): ?>
                <?php include('../../constant/alerts.php'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>