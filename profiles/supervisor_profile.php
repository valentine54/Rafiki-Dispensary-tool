<?php
session_start();
require_once '../database_connection.php';

// Check if the user is a supervisor or administrator
if ($_SESSION['user'] !== 'supervisor' && $_SESSION['user'] !== 'administrator') {
	header('Location: ../errors/not_allowed.php');
	exit;
}

function getAllPharmacies()
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM pharmacy");
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

// Function to get all contracts associated with the supervisor
function getSupervisorContracts($supervisor_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT contract.*, pharmacy.name AS pharmacy_name
		FROM contract
		INNER JOIN pharmaceutical ON contract.pharmaceutical_id = pharmaceutical.pharmaceutical_id
		INNER JOIN pharmacy ON contract.pharmacy_id = pharmacy.pharmacy_id
		INNER JOIN supervisor ON supervisor.pharmaceutical_id = pharmaceutical.pharmaceutical_id
		WHERE supervisor.supervisor_id = :supervisor_id");
	$stmt->bindValue(':supervisor_id', $supervisor_id, PDO::PARAM_INT);
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

// Function to get supervisor details and related pharmaceutical name
function getSupervisorDetails($supervisor_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT supervisor.*, pharmaceutical.name AS pharmaceutical_name
		FROM supervisor
		JOIN pharmaceutical ON pharmaceutical.pharmaceutical_id = supervisor.pharmaceutical_id
		WHERE supervisor.supervisor_id = :supervisor_id");
	$stmt->bindValue(':supervisor_id', $supervisor_id, PDO::PARAM_INT);
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

// Function to create a new contract
function createContract($pharmacy_id, $start_date, $end_date)
{
	global $pdo;
	$supervisor_details = getSupervisorDetails($_GET['supervisor_id']);
	$stmt = $pdo->prepare("INSERT INTO contract (pharmacy_id, pharmaceutical_id, start_date, end_date)
		VALUES (:pharmacy_id, :pharmaceutical_id, :start_date, :end_date)");
	$stmt->bindValue(':pharmacy_id', $pharmacy_id, PDO::PARAM_INT);
	$stmt->bindValue(':pharmaceutical_id', $supervisor_details['pharmaceutical_id'], PDO::PARAM_INT);
	$stmt->bindValue(':start_date', $start_date);
	$stmt->bindValue(':end_date', $end_date);
	$stmt->execute();
}

// Get the supervisor ID from the session
$supervisor_id = $_SESSION['user_id'];

// Get supervisor details and related pharmaceutical name
$supervisor_details = getSupervisorDetails($supervisor_id);

// Get all contracts associated with the supervisor
$supervisor_contracts = getSupervisorContracts($supervisor_id);

// Get all pharmacies
$all_pharmacies = getAllPharmacies();

// Check if a new contract is being created
if (isset($_POST['create_contract'])) {
	$pharmacy_id = $_POST['pharmacy_id'];
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];

	createContract($pharmacy_id, $start_date, $end_date);

	// Refresh the page to reflect the changes
	header('Location: ../profiles/supervisor_profile.php?supervisor_id=' . $supervisor_id);
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Profile</title>
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
		<div class="max-w-full mx-auto" style = "margin: 0 50px;">
		    <!-- Display supervisor details -->
				<h2 class="text-2xl font-semibold text-gray-800 mb-4">Supervisor Details</h2>
				<div class="mb-4">
					<span class="font-semibold">Name:</span> <?php echo $supervisor_details['name']; ?>
				</div>
				<div class="mb-4">
					<span class="font-semibold">Email:</span> <?php echo $supervisor_details['email']; ?>
				</div>
				<div class="mb-4">
					<span class="font-semibold">Mobile Number:</span> <?php echo $supervisor_details['mobile_number']; ?>
				</div>

		    <!-- Link to Associated Pharmaceutical -->
		    <div class="mt-4">
			<h2 class="text-xl font-semibold text-gray-800">Associated Pharmaceutical</h2>
			<p class="text-sm text-gray-600">Pharmaceutical Name: <a href="../profiles/pharmaceutical_profile.php?pharmaceutical_id=<?php echo $supervisor_details['pharmaceutical_id']; ?>" class="text-blue-500"><?php echo $supervisor_details['pharmaceutical_name']; ?></a></p>
		    </div>

		    <!-- Create Contract Section -->
		    <div class="mt-10">
			<h2 class="text-2xl font-semibold text-gray-800">Create Contract</h2>
			<form action="supervisor_profile.php?supervisor_id=<?php echo $supervisor_id; ?>" method="post" class="mt-4">
			    <div class="grid grid-cols-2 gap-4">
				<div>
				    <label for="pharmacy_id" class="block text-sm font-medium text-gray-700">Pharmacy:</label>
				    <select id="pharmacy_id" name="pharmacy_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
					<!-- Populate with pharmacy options -->
					<?php foreach ($all_pharmacies as $pharmacy) : ?>
					    <option value="<?php echo $pharmacy['pharmacy_id']; ?>"><?php echo $pharmacy['name']; ?></option>
					<?php endforeach; ?>
				    </select>
				</div>
				<div>
				    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
				    <input type="date" id="start_date" name="start_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
				</div>
				<div>
				    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
				    <input type="date" id="end_date" name="end_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
				</div>
			    </div>
			    <div class="mt-4">
				<button type="submit" name="create_contract" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Create Contract</button>
			    </div>
			</form>
		    </div>

		    <!-- Contracts Table -->
		    <?php if (!empty($supervisor_contracts)) : ?>
			<div class="mt-10">
			    <h2 class="text-2xl font-semibold text-gray-800">Associated Contracts</h2>
			    <table class="mt-4 w-full border-collapse">
				<thead>
				    <tr>
					<th class="px-4 py-2">Contract ID</th>
					<th class="px-4 py-2">Pharmacy</th>
					<th class="px-4 py-2">Start Date</th>
					<th class="px-4 py-2">End Date</th>
					<th class="px-4 py-2">Status</th>
					<th class="px-4 py-2">Period</th>
					<th class="px-4 py-2">View Contract</th>
				    </tr>
				</thead>
				<tbody>
				    <?php foreach ($supervisor_contracts as $contract) : ?>
					<tr>
					    <td class="border px-4 py-2"><?php echo $contract['contract_id']; ?></td>
					    <td class="border px-4 py-2"><?php echo $contract['pharmacy_name']; ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($contract['start_date']))->format('Y-m-d'); ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($contract['end_date']))->format('Y-m-d'); ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($contract['end_date']))->format('Y-m-d') < (new DateTime())->format('Y-m-d') ? 'Expired' : 'Active'; ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($contract['start_date']))->diff(new DateTime($contract['end_date']))->format('%a days'); ?></td>

					    <td class="border px-4 py-2">
						<a href="../profiles/contract_profile.php?contract_id=<?php echo $contract['contract_id']; ?>" class="text-blue-500">View Contract</a>
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
