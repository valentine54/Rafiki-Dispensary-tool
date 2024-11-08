<?php
require_once '../database_connection.php';

// Function to get patient details
function getPatientDetails($patient_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM patient WHERE patient_id = :patient_id");
	$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all assigned doctors for the patient
function getAssignedDoctors($patient_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT doctor.*, patient_doctor_assignment.is_primary
		FROM doctor
		JOIN patient_doctor_assignment ON doctor.doctor_id = patient_doctor_assignment.doctor_id
		WHERE patient_doctor_assignment.patient_id = :patient_id");
	$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if the user is logged in and is allowed (patient or administrator)
session_start();
if (!isset($_SESSION['user']) && ($_SESSION['user'] !== 'patient' && $_SESSION['user'] !== 'administrator')) {
	header("Location: ../errors/not_allowed.php");
	exit();
}

// Get the patient_id from the URL
if (!isset($_GET['patient_id']) || !is_numeric($_GET['patient_id'])) {
	header("Location: ../errors/not_found.php");
	exit();
}

$patient_id = $_GET['patient_id'];

// Get the patient details
$patient = getPatientDetails($patient_id);

if (!$patient) {
	header("Location: ../errors/not_found.php");
	exit();
}

// If the user is an administrator, check if the patient exists in the database
if ($_SESSION['user'] === 'administrator') {
	$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM patient WHERE patient_id = :patient_id");
	$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
	$stmt->execute();
	$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

	if ($count === 0) {
		header("Location: ../errors/not_found.php");
		exit();
	}
}

// Function to get all doctors for the select field
function getAllDoctors()
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM doctor");
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all assigned doctors for the patient
$assigned_doctors = getAssignedDoctors($patient_id);

// Get all doctors for the select field
$all_doctors = getAllDoctors();

// Function to get all assigned prescriptions for the patient
function getAssignedPrescriptions($patient_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT prescription.*, drug.trade_name AS drug_name, doctor.name AS doctor_name, pharmacy.name AS pharmacy_name, prescription.is_assigned
		FROM prescription
		JOIN drug ON prescription.drug_id = drug.drug_id
		JOIN patient_doctor_assignment ON prescription.patient_doctor_assignment_id = patient_doctor_assignment.patient_doctor_assignment_id
		JOIN doctor ON patient_doctor_assignment.doctor_id = doctor.doctor_id
		JOIN contract ON drug.contract_id = contract.contract_id
		JOIN pharmacy ON contract.pharmacy_id = pharmacy.pharmacy_id
		WHERE patient_doctor_assignment.patient_id = :patient_id");
	$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
	try
	{
		$stmt->execute();
	}
	catch (PDOException $e)
	{
		return array();
	}

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all assigned prescriptions for the patient
$assigned_prescriptions = getAssignedPrescriptions($patient_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
    <div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
		<div class="max-w-full mx-auto">
		    <h1 class="text-4xl font-semibold text-gray-800 mb-8">Patient Profile</h1>
		    <div class="mt-6">
			<div class="flex items-center justify-between">
			    <span class="text-gray-500">Patient ID:</span>
			    <span class="text-gray-700 font-semibold"><?php echo $patient['patient_id']; ?></span>
			</div>
			<div class="flex items-center justify-between mt-4">
			    <span class="text-gray-500">Name:</span>
			    <span class="text-gray-700 font-semibold"><?php echo $patient['name']; ?></span>
			</div>
			<div class="flex items-center justify-between mt-4">
			    <span class="text-gray-500">Email:</span>
			    <span class="text-gray-700 font-semibold"><?php echo $patient['email']; ?></span>
			</div>
			<div class="flex items-center justify-between mt-4">
			    <span class="text-gray-500">Mobile Number:</span>
			    <span class="text-gray-700 font-semibold"><?php echo $patient['mobile_number']; ?></span>
			</div>
		    </div>
		    
		    <!-- Edit Profile Button -->
		    <?php if ($_SESSION['user'] === 'patient' || $_SESSION['user'] === 'administrator') : ?>
			<div class="mt-10">
			    <a href="../updates/update_patient.php?patient_id=<?php echo $patient_id; ?>" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Edit Profile</a>
			</div>
		    <?php endif; ?>


		    <!-- Assigned Doctors Table -->
		    <?php if (!empty($assigned_doctors)) : ?>
			<div class="mt-10">
			    <h2 class="text-2xl font-semibold text-gray-800">Assigned Doctors</h2>
			    <table class="mt-4 w-full border-collapse">
				<thead>
				    <tr>
					<th class="px-4 py-2">Doctor ID</th>
					<th class="px-4 py-2">Name</th>
					<th class="px-4 py-2">Email</th>
					<th class="px-4 py-2">Mobile Number</th>
					<th class="px-4 py-2">Specialization</th>
				    </tr>
				</thead>
				<tbody>
				    <?php foreach ($assigned_doctors as $doctor) : ?>
					<tr>
					    <td class="border px-4 py-2"><?php echo $doctor['doctor_id']; ?></td>
					    <td class="border px-4 py-2"><?php echo $doctor['name']; ?></td>
					    <td class="border px-4 py-2"><?php echo $doctor['email']; ?></td>
					    <td class="border px-4 py-2"><?php echo $doctor['mobile_number']; ?></td>
					    <td class="border px-4 py-2"><?php echo $doctor['specialization']; ?></td>
					</tr>
				    <?php endforeach; ?>
				</tbody>
			    </table>
			</div>
		    <?php endif; ?>

<?php if (!empty($assigned_prescriptions)) : ?>
    <div class="mt-10">
	<h2 class="text-2xl font-semibold text-gray-800">Assigned Prescriptions</h2>
	<table class="mt-4 w-full border-collapse">
	    <thead>
		<tr>
		    <th class="px-4 py-2">Prescription ID</th>
		    <th class="px-4 py-2">Drug Assigned</th>
		    <th class="px-4 py-2">Doctor Assigned</th>
		    <th class="px-4 py-2">Pharmacy Involved</th>
		    <th class="px-4 py-2">Is Dispensed?</th>
		</tr>
	    </thead>
	    <tbody>
		<?php foreach ($assigned_prescriptions as $prescription) : ?>
		    <tr>
			<td class="border px-4 py-2"><?php echo $prescription['prescription_id']; ?></td>
			<td class="border px-4 py-2"><?php echo $prescription['drug_name']; ?></td>
			<td class="border px-4 py-2"><?php echo $prescription['doctor_name']; ?></td>
			<td class="border px-4 py-2"><?php echo $prescription['pharmacy_name']; ?></td>
			<td class="border px-4 py-2"><?php echo $prescription['is_assigned'] ? 'Yes' : 'No'; ?></td>
		    </tr>
		<?php endforeach; ?>
	    </tbody>
	</table>
    </div>
<?php endif; ?>
		</div>
    <?php echo $footer; ?>
</body>

</html>
