<?php
require_once '../database_connection.php';

// Function to get all possible drug forms
function getAllDrugForms()
{
	return array(
		"Tablet",
		"Capsule",
		"Liquid",
		"Injection",
		"Cream",
		"Ointment",
		"Drops",
		"Inhaler"
	);
}

function getDoctorId() {
	global $pdo;
	$stmt = $pdo->query("SELECT doctor_id from patient_doctor_assignment 
		WHERE patient_doctor_assignment_id = " . $_GET['patient_doctor_assignment_id']);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all registered drugs in the system
function getAllDrugs()
{
	global $pdo;
	$stmt = $pdo->query("SELECT drug_id, trade_name FROM drug");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to handle prescription addition and insert prescription into the database
function addPrescription($drug_id, $patient_doctor_assignment_id, $dosage, $frequency, $cost, $start_date, $end_date)
{
	global $pdo;

	try {
		$stmt = $pdo->prepare("INSERT INTO prescription (drug_id, patient_doctor_assignment_id, dosage, frequency, cost, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$drug_id, $patient_doctor_assignment_id, $dosage, $frequency, $cost, $start_date, $end_date]);
		return true;
	} catch (PDOException $e) {
		// Handle any errors that may occur during prescription addition
		return false;
	}
}

// Check if the user is logged in and is allowed (doctor)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'doctor' && $_SESSION['user'] !== 'administrator')) {
	header("Location: ../errors/not_allowed.php");
	exit();
}

// Check if the doctor_patient_id is provided in the URL
if (!isset($_GET['patient_doctor_assignment_id'])) {
	$error_message = "Please include the patient_doctor_assignment_id in the URL.";
}

// Get all possible drug forms to populate the select field
$drug_forms = getAllDrugForms();

// Get all registered drugs to populate the select field for drugs
$drugs = getAllDrugs();

$patient_doctor_assignment_id = $_GET['patient_doctor_assignment_id'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	// Process the form data
	$drug_id = $_POST["drug_id"];
	$dosage = $_POST["dosage"];
	$frequency = $_POST["frequency"];
	$cost = $_POST["cost"];
	$start_date = $_POST["start_date"];
	$end_date = $_POST["end_date"];

	// Perform basic form validation
	if (empty($drug_id) || empty($dosage) || empty($frequency) || empty($cost) || empty($start_date) || empty($end_date)) {
		$error_message = "All fields are required.";
	} else {
		// Attempt to add the prescription
		
		if (addPrescription($drug_id, $patient_doctor_assignment_id, $dosage, $frequency, $cost, $start_date, $end_date)) {
			header("Location: ../profiles/doctor_profile.php?doctor_id=" . getDoctorId()[0]['doctor_id']);
			exit;
		} else {
			$error_message = "An error occurred while adding the prescription. Please try again later.";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Prescription</title>
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
	<h2 class="mb-4 text-2xl font-bold">Add Prescription</h2>
	<?php if (isset($error_message)) : ?>
	    <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
	<?php endif; ?>
	<?php if (isset($success_message)) : ?>
	    <div class="mb-4 p-3 bg-green-200 text-green-700 rounded"><?php echo $success_message; ?></div>
	<?php endif; ?>
	<?php if (!isset($_GET['patient_doctor_assignment_id'])) : ?>
	    <div class="mb-4 p-3 bg-yellow-200 text-yellow-700 rounded">Please include the patient_doctor_assignmentid in the URL.</div>
	<?php endif; ?>
	<form method="POST" action="">
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="drug_id">Drug:</label>
		<select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="drug_id" name="drug_id">
		    <option value="" disabled selected>Select a drug</option>
		    <?php foreach ($drugs as $drug) : ?>
			<option value="<?php echo $drug['drug_id']; ?>"><?php echo $drug['trade_name']; ?></option>
		    <?php endforeach; ?>
		</select>
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="dosage">Dosage:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="dosage" type="text" name="dosage" placeholder="Enter dosage">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="frequency">Frequency:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="frequency" type="text" name="frequency" placeholder="Enter frequency">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="cost">Cost:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="cost" type="number" name="cost" placeholder="Enter cost">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">Start Date:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="start_date" type="date" name="start_date">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">End Date:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="end_date" type="date" name="end_date">
	    </div>
	    <div class="flex items-center justify-between">
		<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Add Prescription</button>
	    </div>
	</form>
    </div>
</div>
<?php echo $footer; ?>
</body>

</html>
