<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get all pharmacies from the database
function getAllPharmacies()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT pharmacy_id, name FROM pharmacy");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to handle pharmacist registration and insert pharmacist into the database
function registerPharmacist($pharmacy_id, $name, $email, $mobile_number, $password)
{
    global $pdo;

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO pharmacist (pharmacy_id, name, email, mobile_number, password_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$pharmacy_id, $name, $email, $mobile_number, $hashedPassword]);
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

// Get all pharmacies to populate the select field
$pharmacies = getAllPharmacies();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $pharmacy_id = $_POST["pharmacy_id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $password = $_POST["password"];

    // Perform basic form validation
    if (empty($pharmacy_id) || empty($name) || empty($email) || empty($mobile_number) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to register the pharmacist
        if (registerPharmacist($pharmacy_id, $name, $email, $mobile_number, $password)) {
            $success_message = "Pharmacist registration successful.";
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
    <title>Register Pharmacist</title>
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
        <h2 class="mb-4 text-2xl font-bold">Register Pharmacist</h2>
        <?php if (isset($success_message)) : ?>
            <div class="mb-4 p-3 bg-green-200 text-green-700 rounded"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="pharmacy_id">Pharmacy:</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="pharmacy_id" name="pharmacy_id">
                    <option value="" disabled selected>Select a pharmacy</option>
                    <?php foreach ($pharmacies as $pharmacy) : ?>
                        <option value="<?php echo $pharmacy['pharmacy_id']; ?>"><?php echo $pharmacy['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" placeholder="Enter pharmacist's name">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" placeholder="Enter pharmacist's email">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mobile_number">Mobile Number:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" placeholder="Enter pharmacist's mobile number">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" placeholder="Enter a password for the pharmacist">
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
