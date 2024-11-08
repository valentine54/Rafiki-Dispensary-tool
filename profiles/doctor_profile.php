<?php
session_start();
require_once '../database_connection.php';

// Check if the user is a doctor or administrator
if ($_SESSION['user'] !== 'doctor' && $_SESSION['user'] !== 'administrator') {
	header('Location: ../errors/not_allowed.php');
	exit;
}

// Function to get the doctor details
function getDoctorDetails($doctor_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctor_id = :doctor_id");
	$stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all assigned patients for the doctor
function getAssignedPatients($doctor_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT patient.*, patient_doctor_assignment.patient_doctor_assignment_id, patient_doctor_assignment.date_created
		FROM patient
		JOIN patient_doctor_assignment ON patient.patient_id = patient_doctor_assignment.patient_id
		WHERE patient_doctor_assignment.doctor_id = :doctor_id");
	$stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
	$stmt->execute();
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

// Function to get all patients not assigned to any doctor
function getUnassignedPatients()
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT *
		FROM patient
		WHERE patient_id NOT IN (SELECT patient_id FROM patient_doctor_assignment)");
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

// Function to assign a patient to the doctor
function assignPatientToDoctor($doctor_id, $patient_id)
{
	global $pdo;

	$stmt = $pdo->prepare("INSERT INTO patient_doctor_assignment (doctor_id, patient_id) VALUES (:doctor_id, :patient_id)");
	$stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
	$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
	$stmt->execute();
}

// Function to unassign a patient from the doctor
function unassignPatientFromDoctor($doctor_id, $patient_id)
{
	global $pdo;

	$stmt = $pdo->prepare("DELETE FROM patient_doctor_assignment WHERE doctor_id = :doctor_id AND patient_id = :patient_id");
	$stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
	$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
	$stmt->execute();
}

// Get the doctor ID from the session
$doctor_id = $_SESSION['user_id'];

// Get the doctor details
$doctor_details = getDoctorDetails($doctor_id);

// Get all assigned patients for the doctor
$assigned_patients = getAssignedPatients($doctor_id);

// Get all patients not assigned to any doctor
$unassigned_patients = getUnassignedPatients();

// Check if a patient is being assigned/unassigned
if (isset($_POST['assign_patient'])) {
	$patient_id = $_POST['patient_id'];

	// Check if the patient is being assigned or unassigned
	if (isset($_POST['unassign'])) {
		unassignPatientFromDoctor($doctor_id, $patient_id);
	} else {
		assignPatientToDoctor($doctor_id, $patient_id);
	}

	// Refresh the page to reflect the changes
	header('Location: doctor_profile.php?doctor_id=' . $doctor_id);
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Doctor Profile</title>
		<!-- Moment.js CDN -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
		<!-- Tailwind CSS -->
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>

	<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
		<div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
			<div class="max-w-full mx-auto">
				<h2 class="text-2xl font-semibold text-gray-800 mb-4">Doctor Details</h2>
				<div class="mb-4">
					<span class="font-semibold">Name:</span> <?php echo $doctor_details['name']; ?>
				</div>
				<div class="mb-4">
					<span class="font-semibold">Email:</span> <?php echo $doctor_details['email']; ?>
				</div>
				<div class="mb-4">
					<span class="font-semibold">Mobile Number:</span> <?php echo $doctor_details['mobile_number']; ?>
				</div>

				<!-- Assign/Unassign Patient Section -->
				<div class="mt-10">
					<h2 class="text-2xl font-semibold text-gray-800">Assigned Patients</h2>
					<table class="mt-4 w-full border-collapse">
						<thead>
							<tr>
								<th class="px-4 py-2">Patient ID</th>
								<th class="px-4 py-2">Name</th>
								<th class="px-4 py-2">Email</th>
								<th class="px-4 py-2">Mobile Number</th>
								<th class="px-4 py-2">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($assigned_patients as $patient) : ?>
							<tr>
								<td class="border px-4 py-2"><?php echo $patient['patient_id']; ?></td>
								<td class="border px-4 py-2"><?php echo $patient['name']; ?></td>
								<td class="border px-4 py-2"><?php echo $patient['email']; ?></td>
								<td class="border px-4 py-2"><?php echo $patient['mobile_number']; ?></td>
								<td class="border px-4 py-2">
									<a href="../registration/add_prescription.php?patient_doctor_assignment_id=<?php echo $patient['patient_doctor_assignment_id']; ?>" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Assign Prescription</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- Unassigned Patients Section -->
				<?php if (!empty($unassigned_patients)) : ?>
				<div class="mt-10">
					<h2 class="text-2xl font-semibold text-gray-800">Unassigned Patients</h2>
					<table class="mt-4 w-full border-collapse">
						<thead>
							<tr>
								<th class="px-4 py-2">Patient ID</th>
								<th class="px-4 py-2">Name</th>
								<th class="px-4 py-2">Email</th>
								<th class="px-4 py-2">Mobile Number</th>
								<th class="px-4 py-2">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($unassigned_patients as $patient) : ?>
							<tr>
								<td class="border px-4 py-2"><?php echo $patient['patient_id']; ?></td>
								<td class="border px-4 py-2"><?php echo $patient['name']; ?></td>
								<td class="border px-4 py-2"><?php echo $patient['email']; ?></td>
								<td class="border px-4 py-2"><?php echo $patient['mobile_number']; ?></td>
								<td class="border px-4 py-2">
									<!-- Assign/Unassign Patient Button -->
									<?php if (in_array($patient['patient_id'], array_column($assigned_patients, 'patient_id'))) : ?>
									<form action="doctor_profile.php?doctor_id=<?php echo $doctor_id; ?>" method="post">
										<input type="hidden" name="patient_id" value="<?php echo $patient['patient_id']; ?>">
										<button type="submit" name="unassign" class="px-4 py-2 bg-red-500 text-white rounded-lg">Unassign</button>
									</form>
									<?php else : ?>
									<form action="doctor_profile.php?doctor_id=<?php echo $doctor_id; ?>" method="post">
										<input type="hidden" name="patient_id" value="<?php echo $patient['patient_id']; ?>">
										<button type="submit" name="assign_patient" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Assign</button>
									</form>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>
		</div>
    <?php echo $footer; ?>
	</body>
</html>

