<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get patient details by patient_id
function getPatientDetails($patient_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM patient WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to update patient details in the database
function updatePatientDetails($patient_id, $name, $email, $mobile_number, $social_security_number, $password_hash)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE patient SET name = ?, email = ?, mobile_number = ?, social_security_number = ?, password_hash = ? WHERE patient_id = ?");
        $stmt->execute([$name, $email, $mobile_number, $social_security_number, $password_hash, $patient_id]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during patient details update
        return false;
    }
}

// Check if the user is logged in and is allowed (patient or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'patient' && $_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the patient_id is provided in the URL
if (!isset($_GET['patient_id'])) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get patient details by patient_id
$patient_id = $_GET['patient_id'];
$patient_details = getPatientDetails($patient_id);

// Check if the patient exists
if (!$patient_details) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Set default values for the form fields
$name = $patient_details['name'];
$email = $patient_details['email'];
$mobile_number = $patient_details['mobile_number'];
$social_security_number = $patient_details['social_security_number'];
$password = $patient_details['password_hash'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $social_security_number = $_POST["social_security_number"];
    $password = $_POST["password"];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Perform basic form validation
    if (empty($name) || empty($email) || empty($mobile_number) || empty($social_security_number) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to update patient details
        if (updatePatientDetails($patient_id, $name, $email, $mobile_number, $social_security_number, $password_hash)) {
            header("Location: ../profiles/patient_profile.php?patient_id=" . $patient_id);
            exit();
        } else {
            $error_message = "An error occurred while updating patient details. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Edit Patient Details</h2>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?patient_id=" . $patient_id; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="<?php echo $name; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mobile_number">Mobile Number:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" value="<?php echo $mobile_number; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="social_security_number">Social Security Number:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="social_security_number" type="text" name="social_security_number" value="<?php echo $social_security_number; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="text" name="password" value="<?php echo $password; ?>">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Update Details</button>
            </div>
        </form>
    </div>
</body>

</html>
