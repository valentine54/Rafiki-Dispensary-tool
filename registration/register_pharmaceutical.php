<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to handle pharmaceutical registration and insert pharmaceutical into the database
function registerPharmaceutical($name, $location, $email, $mobile_number)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO pharmaceutical (name, location, email, mobile_number) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $location, $email, $mobile_number]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during registration
        return false;
    }
}

// Check if the user is logged in and is an administrator
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $name = $_POST["name"];
    $location = $_POST["location"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];

    // Perform basic form validation
    if (empty($name) || empty($location) || empty($email) || empty($mobile_number)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to register the pharmaceutical
        if (registerPharmaceutical($name, $location, $email, $mobile_number)) {
            $success_message = "Pharmaceutical registration successful.";
        } else {
            $error_message = "An error occurred during registration. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pharmaceutical</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Register Pharmaceutical</h2>
        <?php if (isset($success_message)) : ?>
            <div class="mb-4 p-3 bg-green-200 text-green-700 rounded"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" placeholder="Enter pharmaceutical's name">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">Location:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="location" type="text" name="location" placeholder="Enter pharmaceutical's location">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" placeholder="Enter pharmaceutical's email">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mobile_number">Mobile Number:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" placeholder="Enter pharmaceutical's mobile number">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Register</button>
            </div>
        </form>
    </div>
</div>
<?php echo $footer; ?>
</body>

</html>
