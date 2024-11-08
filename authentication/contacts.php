<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Rafiki Drug Dispenser</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-semibold text-center mb-6">Contact Us</h1>
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Rafiki Drug Dispenser</h2>
            <p class="text-lg mb-4">Address: 1234 Any Street, Nakuru, Kenya</p>
            <p class="text-lg mb-4">Phone: +254 123 456 789</p>
            <p class="text-lg mb-4">Email: info@rafikidrugdispenser.com</p>
            <div class="text-lg mb-4">
                <p>Opening Hours:</p>
                <ul class="list-disc ml-6">
                    <li>Monday - Friday: 8:00 AM - 5:00 PM</li>
                    <li>Saturday: 8:00 AM - 12:00 PM</li>
                    <li>Sunday: Closed</li>
                </ul>
            </div>
            <div class="text-lg mb-4">
                <p>For any inquiries or assistance, please feel free to contact us. Our dedicated team is ready to
                    provide
                    you with professional and friendly service.</p>
            </div>
        </div>
    </div>
    <?php echo $footer; ?>
</body>

</html>
