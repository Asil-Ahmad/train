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
    function sendEmail($toEmail, $subject, $body)
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
            $mail->addAddress($toEmail);
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

    // Example usage of sendEmail function
    // $email = 'asil.infoseek@gmail.com';
    // $subject = 'Test Email';
    // $body = 'This is a test email.';

    // if (sendEmail($toEmail, $subject, $body)) {
    //     echo 'Email sent successfully.';
    // } else {
    //     echo 'Failed to send email.';
    // }
    ?>

    <!-- Main Content Wrapper -->
    <div class="flex flex-1 justify-center items-center px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-800 text-center">Login</h2>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com"
                        class="<?php echo isset($email) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border  focus:border-blue-500 transition-all">
                    <?php if (isset($email) && is_string($email)) echo "<p class='text-red-500 text-xs mt-1'>$email</p>"; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••"
                        class="<?php echo isset($password) ? 'border-red-500' : 'border-gray-300' ?> w-full px-4 py-3 rounded-lg border  focus:border-blue-500 transition-all">
                    <?php if (isset($password) && is_string($password)) echo "<p class='text-red-500 text-xs mt-1'>$password</p>"; ?>
                </div>

                <button type="submit"
                    class="w-full bg-[#0055A5] text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none  focus:ring-offset-2 transition-all">
                    Login
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['email']) || empty($_POST['password'])) {
                    $email = empty($_POST['email']) ? "Email is required" : "";
                    $password = empty($_POST['password']) ? "Password is required" : "";
                } else {
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

                    $sql = "SELECT * FROM users WHERE email = '$email'";
                    try {
                        $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
                        $userData = mysqli_query($connection, $sql);
                        $user = mysqli_fetch_assoc($userData);

                        if ($user && password_verify($password, $user['password'])) {
                            $_SESSION['user_id'] = $user['user_id'];
                            $_SESSION['user_name'] = $user['username'];
                            $_SESSION['user_role'] = $user['role'];
                            $_SESSION['user_email'] = $user['email'];


                            header("Location: /train/index.php");
                            exit();
                            // echo $user['email'];
                            //todo send email only if its admin
                            // $toEmail = $user['email'];
                            // $subject = 'BookMyTrain';
                            // $body = 'You have successfully logged in to BookMyTrain.';


                            // if (sendEmail($toEmail, $subject, $body)) {
                            //     echo 'Email sent successfully.';
                            //     header("Location: /train/index.php");
                            //     exit();
                            // } else {
                            //     echo 'Failed to send email.';
                            // }
                        } else {
                            $err = "Invalid email or password";
                        }
                    } catch (mysqli_sql_exception $error) {
                        $err = $error->getMessage();
                    }
                }
            }
            ?>
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
                    }, 3000);
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>