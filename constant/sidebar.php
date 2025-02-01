<?php
session_start();
// echo $_SESSION['user_role'] == 'admin' ? "isAdmin" : 'isUser';
?>

<nav class="bg-[#F5F5F5]">
  <div class="flex justify-center w-full py-4 border-b-2 border-gray-500">
    <h1 class="text-2xl font-bold flex tracking-wide">
      BookMy<span class="bg-[#0055A5] px-1 flex items-center text-[#F5F5F5]">Train</span>
    </h1>
    <!-- <img src="/train/assets/train.png" alt="testx" class="invert" /> -->
  </div>
  <div
    class="flex py-4 justify-end gap-1 bg-[#F5F5F5] text-[#37474F] text-sm font-fine">
    <a
      class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1"
      href="/train/index.php">Dashboard</a>
<!-- Todo If user is admin then see these routes else no -->
    <?php
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
      echo '
  <a class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1" href="/train/src/components/trains/AddTrains.php">Add Trains</a>
  <a class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1" href="/train/src/components/AddStations.php">Add Stations</a>
  <a class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1" href="/train/src/components/Routes.php">Add Routes</a>';
    }
    ?>

    <a
      class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1"
      href="/train/src/components/BookTicket.php">Book Ticket</a>

    <?php
    echo isset($_SESSION['user_id'])
      ? '<a class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1" href="/train/src/components/Logout.php">Logout</a>'
      : '<a class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1" href="/train/src/components/Login.php">Login</a>';
    ?>


    <?php
    echo empty($_SESSION['user_id']) ? '<a
        class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1"
        href="/train/src/components/CreateAccount.php">Sign Up</a>' :

      '<a class="border-b-2 border-transparent hover:border-[#0055A5]  px-5 py-1" href="#">
          <div class="flex items-center gap-1">
              <p class="w-6 h-6 text-center content-center bg-[#0055A5] text-white rounded-full">A</p>
              <span class="font-semibold">' . $_SESSION['user_name'] . '</span>
          </div>
      </a>';
    ?>



  </div>
</nav>