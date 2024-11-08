<?php
session_start();
require_once '../database_connection.php';

// Function to handle patient registration and insert patient into the database
function registerPatient($name, $email, $mobile_number, $social_security_number, $password)
{
	global $pdo;

	// Hash the password
	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

	try {
		$stmt = $pdo->prepare("INSERT INTO patient (name, email, mobile_number, social_security_number, password_hash) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([$name, $email, $mobile_number, $social_security_number, $hashedPassword]);
		return true;
	} catch (PDOException $e) {
		// Handle any errors that may occur during registration
		return false;
	}
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	// Process the form data
	$name = $_POST["name"];
	$email = $_POST["email"];
	$mobile_number = $_POST["mobile_number"];
	$social_security_number = $_POST["social_security_number"];
	$password = $_POST["password"];

	// Perform basic form validation
	if (empty($name) || empty($email) || empty($mobile_number) || empty($social_security_number) || empty($password)) {
		$error_message = "All fields are required.";
	} else {
		// Attempt to register the patient
		if (registerPatient($name, $email, $mobile_number, $social_security_number, $password)) {
			$success_message = "Patient registration successful.";
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
    <title>Register Patient</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-md rounded w-96 px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Register Patient</h2>
	<?php if (isset($success_message)) : ?>
	    <div class="mb-4 p-3 bg-green-200 text-green-700 rounded"><?php echo $success_message; ?></div>
	<?php endif; ?>
	<?php if (isset($error_message)) : ?>
	    <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
	<?php endif; ?>
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" placeholder="Enter patient's name">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" placeholder="Enter patient's email">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="mobile_number">Mobile Number:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" placeholder="Enter patient's mobile number">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="social_security_number">Social Security Number:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="social_security_number" type="text" name="social_security_number" placeholder="Enter patient's social security number">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" placeholder="Enter a password for the patient">
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
