<?
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
<div class="flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full px-6 py-8 bg-white border rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-center">Page Not Found</h2>
        <p class="mt-4 text-center">The requested page could not be found.</p>
    </div>
</div>
<?php echo $footer; ?>
</body>

</html>
