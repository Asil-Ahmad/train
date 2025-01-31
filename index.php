<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include('constant/header.html');
include('constant/sidebar.html');
echo $_SESSION['user_name'];
?>

<body class=" flex flex-col min-h-screen  bg-cover bg-center bg-no-repeat">
    <!-- bg-[url('/train/assets/trainbackground.jpg')] ] -->
    <div class="flex-grow flex flex-col justify-evenly items-center ">
        <img src="/train/assets/Indian_Railways.png" alt="" class="w-[200px] h-[200px]">
        <div class="bg-white w-full py-4 flex flex-col justify-center items-center relative">


            <h1 class="text-4xl uppercase tracking-wider font-medium text-center w-full z-10 scale-y-90">
                Online railway reservation system
            </h1>
            <hr>
            <div class="relative">
                <a href="/train/src/components/BookTicket.php" class="z-50 text-[#37474F] hover:bg-[#D32F2F] transition-all duration-200 font-bold hover:text-white px-2">Book Your Tickets Now</a>
                <lottie-player src="/train/assets/ticket.json" background="transparent" speed="1"
                    style="width: 100px; height: 100px;" class="absolute -z-10 right-[25%] top-[50%]" loop autoplay>
                </lottie-player>
            </div>
            <lottie-player src="/train/assets/train.json" background="transparent" speed="1"
                style="width: 300px; height: 300px;" class="absolute left-0 " autoplay>
            </lottie-player>
            <lottie-player src="/train/assets/train.json" background="transparent" speed="1"
                style="width: 300px; height: 300px;" class="absolute right-0 scale-x-[-1]" autoplay>
            </lottie-player>
        </div>
    </div>
</body>

</html>