<?php
require_once '../database_connection.php';

// Function to get pharmacist details by pharmacist_id
function getPharmacistDetails($pharmacist_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM pharmacist WHERE pharmacist_id = ?");
	$stmt->execute([$pharmacist_id]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all pharmacies
function getAllPharmacies()
{
	global $pdo;

	$stmt = $pdo->query("SELECT * FROM pharmacy");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to update pharmacist details in the database
function updatePharmacistDetails($pharmacist_id, $pharmacy_id, $name, $email, $mobile_number)
{
	global $pdo;

	try {
		$stmt = $pdo->prepare("UPDATE pharmacist SET pharmacy_id = ?, name = ?, email = ?, mobile_number = ? WHERE pharmacist_id = ?");
		$stmt->execute([$pharmacy_id, $name, $email, $mobile_number, $pharmacist_id]);
		return true;
	} catch (PDOException $e) {
		// Handle any errors that may occur during pharmacist details update
		return false;
	}
}

// Check if the user is logged in and is allowed (pharmacist or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'pharmacist' && $_SESSION['user'] !== 'administrator')) {
	header("Location: ../errors/not_allowed.php");
	exit();
}

// Check if the pharmacist_id is provided in the URL
if (!isset($_GET['pharmacist_id'])) {
	header("Location: ../errors/not_found.php");
	exit();
}

// Get pharmacist details by pharmacist_id
$pharmacist_id = $_GET['pharmacist_id'];
$pharmacist_details = getPharmacistDetails($pharmacist_id);

// Check if the pharmacist exists
if (!$pharmacist_details) {
	header("Location: ../errors/not_found.php");
	exit();
}

// Get all pharmacies
$pharmacies = getAllPharmacies();

// Set default values for the form fields
$pharmacy_id = $pharmacist_details['pharmacy_id'];
$name = $pharmacist_details['name'];
$email = $pharmacist_details['email'];
$mobile_number = $pharmacist_details['mobile_number'];
$password = $pharmacist_details['password_hash'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	// Process the form data
	$pharmacy_id = $_POST["pharmacy_id"];
	$name = $_POST["name"];
	$email = $_POST["email"];
	$mobile_number = $_POST["mobile_number"];

	// Perform basic form validation
	if (empty($pharmacy_id) || empty($name) || empty($email) || empty($mobile_number)) {
		$error_message = "All fields are required.";
	} else {
		// Attempt to update pharmacist details
		if (updatePharmacistDetails($pharmacist_id, $pharmacy_id, $name, $email, $mobile_number)) {
			header("Location: ../profiles/pharmacist_profile.php?pharmacist_id=" . $pharmacist_id);
			exit();
		} else {
			$error_message = "An error occurred while updating pharmacist details. Please try again later.";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pharmacist Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
	<h2 class="mb-4 text-2xl font-bold">Edit Pharmacist Details</h2>
	<?php if (isset($error_message)) : ?>
	    <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
	<?php endif; ?>
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?pharmacist_id=" . $pharmacist_id; ?>">
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="pharmacy_id">Pharmacy:</label>
		<select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="pharmacy_id" name="pharmacy_id">
		    <?php foreach ($pharmacies as $pharmacy) : ?>
			<option value="<?php echo $pharmacy['pharmacy_id']; ?>" <?php if ($pharmacy['pharmacy_id'] == $pharmacy_id) echo 'selected'; ?>><?php echo $pharmacy['name']; ?></option>
		    <?php endforeach; ?>
		</select>
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="<?php echo $name; ?>">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="name">Phone Number:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" value="<?php echo $mobile_number; ?>">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="<?php echo $email; ?>">
	    </div>
	    <div class="mb-4">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Update Details</button>
	    </div>
	</form>
    </div>
</body>

</html>
