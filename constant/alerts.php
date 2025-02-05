<?php
// Example usage: set either a success or error message
// $err = "Your action was successful!";
// Or for an error message:
// $err = "Something went wrong. Please try again.";
?>

<!-- Toast Notification -->
<?php if (isset($success)): ?>
    <div id="successToast" class="fixed  z-50 top-4 right-4 py-10 px-8 bg-green-50 rounded-lg shadow-lg transition-opacity duration-500">
        <p class="text-green-600 text-sm flex gap-2 items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="green" class="size-10">
                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
            </svg>
            <?php echo $success; ?></p>
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
    <div id="errorToast" class="fixed z-50 top-4 right-4 py-10 px-8  bg-red-50 rounded-lg shadow-lg transition-opacity duration-500">
        <p class="text-red-600 text-sm flex gap-2 items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="red" class="size-10">
                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
            </svg>
            <?php echo $err; ?></p>
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