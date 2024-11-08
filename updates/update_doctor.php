<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get doctor details by doctor_id
function getDoctorDetails($doctor_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctor_id = ?");
    $stmt->execute([$doctor_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to update doctor details in the database
function updateDoctorDetails($doctor_id, $name, $email, $mobile_number, $specialization, $password_hash)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE doctor SET name = ?, email = ?, mobile_number = ?, specialization = ?, password_hash = ? WHERE doctor_id = ?");
        $stmt->execute([$name, $email, $mobile_number, $specialization, $password_hash, $doctor_id]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during doctor details update
        return false;
    }
}

// Check if the user is logged in and is allowed (doctor or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'doctor' && $_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the doctor_id is provided in the URL
if (!isset($_GET['doctor_id'])) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get doctor details by doctor_id
$doctor_id = $_GET['doctor_id'];
$doctor_details = getDoctorDetails($doctor_id);

// Check if the doctor exists
if (!$doctor_details) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Set default values for the form fields
$name = $doctor_details['name'];
$email = $doctor_details['email'];
$mobile_number = $doctor_details['mobile_number'];
$specialization = $doctor_details['specialization'];
$password = $doctor_details['password_hash'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $specialization = $_POST["specialization"];
    $password = $_POST["password"];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Perform basic form validation
    if (empty($name) || empty($email) || empty($mobile_number) || empty($specialization) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to update doctor details
        if (updateDoctorDetails($doctor_id, $name, $email, $mobile_number, $specialization, $password_hash)) {
            header("Location: ../profiles/doctor_profile.php?doctor_id=" . $doctor_id);
            exit();
        } else {
            $error_message = "An error occurred while updating doctor details. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Edit Doctor Details</h2>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?doctor_id=" . $doctor_id; ?>">
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="specialization">Specialization:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="specialization" type="text" name="specialization" value="<?php echo $specialization; ?>">
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
