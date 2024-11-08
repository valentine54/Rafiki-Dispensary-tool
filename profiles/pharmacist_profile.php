<?php
session_start();
require_once '../database_connection.php';

// Check if the user is a pharmacist or administrator
if ($_SESSION['user'] !== 'pharmacist' && $_SESSION['user'] !== 'administrator') {
	header('Location: ../errors/not_allowed.php');
	exit;
}

// Function to get pharmacist details and related pharmacy name
function getPharmacistDetails($pharmacist_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT pharmacist.*, pharmacy.name AS pharmacy_name
		FROM pharmacist
		JOIN pharmacy ON pharmacist.pharmacy_id = pharmacy.pharmacy_id
		WHERE pharmacist.pharmacist_id = :pharmacist_id");
	$stmt->bindValue(':pharmacist_id', $pharmacist_id, PDO::PARAM_INT);
	try
	{
		$stmt->execute();
	}
	catch (PDOException $e)
	{
		return array();
	}

	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get related prescriptions for the pharmacist
function getRelatedPrescriptions($pharmacist_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT prescription.*, drug.scientific_name, patient.name AS patient_name, prescription.is_assigned
		FROM prescription
		INNER JOIN drug ON prescription.drug_id = drug.drug_id
		INNER JOIN patient_doctor_assignment ON prescription.patient_doctor_assignment_id = patient_doctor_assignment.patient_doctor_assignment_id
		INNER JOIN patient ON patient_doctor_assignment.patient_id = patient.patient_id
		INNER JOIN contract ON contract.contract_id = drug.contract_id
		INNER JOIN pharmacy ON pharmacy.pharmacy_id = contract.pharmacy_id
		INNER JOIN pharmacist ON pharmacist.pharmacy_id = pharmacy.pharmacy_id
		WHERE pharmacist.pharmacist_id = :pharmacist_id");
	$stmt->bindValue(':pharmacist_id', $pharmacist_id, PDO::PARAM_INT);
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

// Get the pharmacist ID from the session
$pharmacist_id = $_SESSION['user_id'];

// Get pharmacist details and related pharmacy name
$pharmacist_details = getPharmacistDetails($pharmacist_id);

// Get related prescriptions for the pharmacist
$related_prescriptions = getRelatedPrescriptions($pharmacist_id);

// Check if a prescription is being assigned/unassigned
if (isset($_POST['update_prescription'])) {
	$prescription_id = $_POST['prescription_id'];

	// Check if the prescription is already assigned
	$stmt = $pdo->prepare("SELECT is_assigned FROM prescription WHERE prescription_id = :prescription_id");
	$stmt->bindValue(':prescription_id', $prescription_id, PDO::PARAM_INT);
	$stmt->execute();
	$is_assigned = $stmt->fetchColumn();

	if (!$is_assigned) {
		$stmt = $pdo->prepare("UPDATE prescription SET is_assigned = :is_assigned WHERE prescription_id = :prescription_id");
		$stmt->bindValue(':is_assigned', 1);
		$stmt->bindValue(':prescription_id', $prescription_id, PDO::PARAM_INT);
		$stmt->execute();
	}

	// Refresh the page to reflect the changes
	header('Location: ../profiles/pharmacist_profile.php?pharmacist_id=' . $pharmacist_id);
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Profile</title>
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
		    <!-- Display pharmacist details here -->
				<h2 class="text-2xl font-semibold text-gray-800 mb-4">Pharmacist Details</h2>
				<div class="mb-4">
					<span class="font-semibold">Name:</span> <?php echo $pharmacist_details['name']; ?>
				</div>
				<div class="mb-4">
					<span class="font-semibold">Email:</span> <?php echo $pharmacist_details['email']; ?>
				</div>
				<div class="mb-4">
					<span class="font-semibold">Mobile Number:</span> <?php echo $pharmacist_details['mobile_number']; ?>
				</div>


		    <!-- Link to Associated Pharmacy -->
		    <div class="mt-4">
			<h2 class="text-xl font-semibold text-gray-800">Associated Pharmacy</h2>
			<p class="text-sm text-gray-600">Pharmacy Name: <a href="../profiles/pharmacy_profile.php?pharmacy_id=<?php echo $pharmacist_details['pharmacy_id']; ?>" class="text-blue-500"><?php echo $pharmacist_details['pharmacy_name']; ?></a></p>
		    </div>

		    <!-- Edit Pharmacist Profile Button -->
		    <div class="mt-4">
			<a href="../updates/update_pharmacist.php?pharmacist_id=<?php echo $pharmacist_id; ?>" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Edit Pharmacist Profile</a>
		    </div>

		    <!-- Related Prescriptions Table -->
		    <?php if (!empty($related_prescriptions)) : ?>
			<div class="mt-10">
			    <h2 class="text-2xl font-semibold text-gray-800">Related Prescriptions</h2>
			    <table class="mt-4 w-full border-collapse">
				<thead>
				    <tr>
					<th class="px-4 py-2">Prescription ID</th>
					<th class="px-4 py-2">Drug Name</th>
					<th class="px-4 py-2">Patient Name</th>
					<th class="px-4 py-2">Dosage</th>
					<th class="px-4 py-2">Frequency</th>
					<th class="px-4 py-2">Cost</th>
					<th class="px-4 py-2">Start Date</th>
					<th class="px-4 py-2">End Date</th>
					<th class="px-4 py-2">Is Assigned</th>
					<th class="px-4 py-2">Action</th>
				    </tr>
				</thead>
				<tbody>
				    <?php foreach ($related_prescriptions as $prescription) : ?>
					<tr>
					    <td class="border px-4 py-2"><?php echo $prescription['prescription_id']; ?></td>
					    <td class="border px-4 py-2"><?php echo $prescription['scientific_name']; ?></td>
					    <td class="border px-4 py-2"><?php echo $prescription['patient_name']; ?></td>
					    <td class="border px-4 py-2"><?php echo $prescription['dosage']; ?></td>
					    <td class="border px-4 py-2"><?php echo $prescription['frequency']; ?></td>
					    <td class="border px-4 py-2"><?php echo $prescription['cost']; ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($prescription['start_date']))->format('Y-m-d'); ?></td>
					    <td class="border px-4 py-2"><?php (new DateTime($prescription['end_date']))->format('Y-m-d'); ?></td>
					    <td class="border px-4 py-2"><?php echo $prescription['is_assigned'] ? 'Assigned' : 'Not Assigned'; ?></td>
					    <td class="border px-4 py-2">
						<?php if (!$prescription['is_assigned']) : ?>
						    <form action="pharmacist_profile.php?pharmacist_id=<?php echo $pharmacist_id; ?>" method="post">
							<input type="hidden" name="prescription_id" value="<?php echo $prescription['prescription_id']; ?>">
							<button type="submit" name="update_prescription" class="text-blue-500" onclick="return confirm('Assign this prescription?')">Assign Prescription</button>
						    </form>
						    <?php else : ?>
						    <p>Assigned</p>
						<?php endif; ?>
					    </td>
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
