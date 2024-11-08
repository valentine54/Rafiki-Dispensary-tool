<?php
session_start();
require_once '../database_connection.php';

// Function to validate administrator credentials
function validateAdministrator($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM administrator WHERE email = ?");
        $stmt->execute([$email]);
        $administrator = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($administrator && password_verify($password, $administrator['password_hash'])) {
            return $administrator;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

// Function to validate patient credentials
function validatePatient($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM patient WHERE email = ?");
        $stmt->execute([$email]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient && password_verify($password, $patient['password_hash'])) {
            return $patient;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

// Function to validate doctor credentials
function validateDoctor($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM doctor WHERE email = ?");
        $stmt->execute([$email]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor && password_verify($password, $doctor['password_hash'])) {
            return $doctor;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

// Function to validate pharmacist credentials
function validatePharmacist($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM pharmacist WHERE email = ?");
        $stmt->execute([$email]);
        $pharmacist = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pharmacist && password_verify($password, $pharmacist['password_hash'])) {
            return $pharmacist;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

// Function to validate supervisor credentials
function validateSupervisor($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM supervisor WHERE email = ?");
        $stmt->execute([$email]);
        $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($supervisor && password_verify($password, $supervisor['password_hash'])) {
            return $supervisor;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
	// Redirect to the appropriate profile page based on the user type
	switch ($_SESSION['user']) {
	case 'administrator':
		header('Location: ../profiles/administrator_profile.php?administrator_id=' . $_SESSION['user_id']);
		exit;
	case 'patient':
		header('Location: ../profiles/patient_profile.php?patient_id=' . $_SESSION['user_id']);
		exit;
	case 'doctor':
		header('Location: ../profiles/doctor_profile.php?doctor_id=' . $_SESSION['user_id']);
		exit;
	case 'pharmacist':
		header('Location: ../profiles/pharmacist_profile.php?pharmacist_id=' . $_SESSION['user_id']);
		exit;
	case 'supervisor':
		header('Location: ../profiles/supervisor_profile.php?supervisor_id=' . $_SESSION['user_id']);
		exit;
	}
}

// Function to validate the user credentials and set session variables
function loginUser($user_type, $user_id)
{
	// Set the session variables
	$_SESSION['user'] = $user_type;
	$_SESSION['user_id'] = $user_id;

	// Redirect to the appropriate profile page based on the user type
	switch ($user_type) {
	case 'administrator':
		header('Location: ../profiles/administrator_profile.php?administrator_id=' . $user_id);
		exit;
	case 'patient':
		header('Location: ../profiles/patient_profile.php?patient_id=' . $user_id);
		exit;
	case 'doctor':
		header('Location: ../profiles/doctor_profile.php?doctor_id=' . $user_id);
		exit;
	case 'pharmacist':
		header('Location: ../profiles/pharmacist_profile.php?pharmacist_id=' . $user_id);
		exit;
	case 'supervisor':
		header('Location: ../profiles/supervisor_profile.php?supervisor_id=' . $user_id);
		exit;
	}
}

// Function to handle login errors
function handleLoginError()
{
	// Redirect to the error page
	header('Location: ../errors/not_allowed.php');
	exit;
}

// Check if the login form is submitted
if (isset($_POST['login'])) {
	// Get the submitted credentials
	$user_type = $_POST['user_type'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	// Validate the user credentials based on the selected user type
	switch ($user_type) {
	case 'administrator':
		$user = validateAdministrator($email, $password);
		break;
	case 'patient':
		$user = validatePatient($email, $password);
		break;
	case 'doctor':
		$user = validateDoctor($email, $password);
		break;
	case 'pharmacist':
		$user = validatePharmacist($email, $password);
		break;
	case 'supervisor':
		$user = validateSupervisor($email, $password);
		break;
	default:
		// Invalid user type, handle error gracefully
		handleLoginError();
	}

	// Check if the user is valid
	if ($user) {
		// User is valid, login and redirect to the appropriate profile page
		loginUser($user_type, $user[$user_type . '_id']);
	} else {
		// Invalid credentials, handle error gracefully
		handleLoginError();
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
	<h2 class="text-2xl font-semibold text-center">Login</h2>
	<form class="mt-8 space-y-6" action="" method="POST">
	    <div class="rounded-md shadow-sm -space-y-px">
		<div>
		    <label for="user_type" class="block text-sm font-medium text-gray-700">Select User Type</label>
		    <select id="user_type" name="user_type" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
			<option value="administrator">Administrator</option>
			<option value="patient">Patient</option>
			<option value="doctor">Doctor</option>
			<option value="pharmacist">Pharmacist</option>
			<option value="supervisor">Supervisor</option>
		    </select>
		</div>
		<div>
		    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
		    <input id="email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
		</div>
		<div>
		    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
		    <input id="password" name="password" type="password" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
		</div>
	    </div>
	    <div>
		<button type="submit" name="login" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
		    Login
		</button>
	    </div>
	</form>
    </div>
</div>
<?php echo $footer; ?>
</body>

</html>
