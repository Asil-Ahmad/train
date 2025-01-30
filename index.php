<!DOCTYPE html>
<html lang="en">

<?php
include('constant/header.html');
include('constant/sidebar.html');
?>

<body class="bg-[#F5F5F5] flex flex-col min-h-screen">
    <div class="flex-grow flex flex-col justify-center items-center relative">
        <h1 class="text-4xl uppercase tracking-wider font-medium text-center w-full z-10 scale-y-90">
            Online railway reservation system
        </h1>
        <hr>
        <div class="relative">
            <a href="/train/src/components/BookTicket.php" class="z-50 text-[#37474F] hover:bg-[#D32F2F] transition-all duration-200 font-bold hover:text-white px-2">Book Your Tickets Now</a>
            <lottie-player src="/train/assets/ticket.json" background="transparent" speed="1"
                style="width: 100px; height: 100px;" class="absolute -z-10 right-[25%] top-[0%]" loop autoplay>
            </lottie-player>
        </div>
        <lottie-player src="/train/assets/train.json" background="transparent" speed="1"
            style="width: 300px; height: 300px;" class="absolute left-0 " autoplay>
        </lottie-player>
        <lottie-player src="/train/assets/train.json" background="transparent" speed="1"
            style="width: 300px; height: 300px;" class="absolute right-0 scale-x-[-1]" autoplay>
        </lottie-player>
    </div>
</body>

</html>