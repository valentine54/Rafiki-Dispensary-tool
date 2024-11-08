<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get pharmaceutical details by pharmaceutical_id
function getPharmaceuticalDetails($pharmaceutical_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM pharmaceutical WHERE pharmaceutical_id = ?");
    $stmt->execute([$pharmaceutical_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to update pharmaceutical details in the database
function updatePharmaceuticalDetails($pharmaceutical_id, $name, $location, $email, $mobile_number)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE pharmaceutical SET name = ?, location = ?, email = ?, mobile_number = ? WHERE pharmaceutical_id = ?");
        $stmt->execute([$name, $location, $email, $mobile_number, $pharmaceutical_id]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during pharmaceutical details update
        return false;
    }
}

// Check if the user is logged in and is allowed (supervisor or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'supervisor' && $_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the pharmaceutical_id is provided in the URL
if (!isset($_GET['pharmaceutical_id'])) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get pharmaceutical details by pharmaceutical_id
$pharmaceutical_id = $_GET['pharmaceutical_id'];
$pharmaceutical_details = getPharmaceuticalDetails($pharmaceutical_id);

// Check if the pharmaceutical exists
if (!$pharmaceutical_details) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Set default values for the form fields
$name = $pharmaceutical_details['name'];
$location = $pharmaceutical_details['location'];
$email = $pharmaceutical_details['email'];
$mobile_number = $pharmaceutical_details['mobile_number'];

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
        // Attempt to update pharmaceutical details
        if (updatePharmaceuticalDetails($pharmaceutical_id, $name, $location, $email, $mobile_number)) {
            header("Location: ../profiles/pharmaceutical_profile.php?pharmaceutical_id=" . $pharmaceutical_id);
            exit();
        } else {
            $error_message = "An error occurred while updating pharmaceutical details. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pharmaceutical Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Edit Pharmaceutical Details</h2>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?pharmaceutical_id=" . $pharmaceutical_id; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="<?php echo $name; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">Location:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="location" type="text" name="location" value="<?php echo $location; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mobile_number">Mobile Number:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" value="<?php echo $mobile_number; ?>">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Update Details</button>
            </div>
        </form>
    </div>
</body>

</html>
