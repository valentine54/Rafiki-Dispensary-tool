<?php
require_once '../database_connection.php';

// Function to get contract details
function getContractDetails($contract_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT contract.*, pharmacy.name AS pharmacy_name, pharmaceutical.name AS pharmaceutical_name FROM contract 
		INNER JOIN pharmacy ON pharmacy.pharmacy_id = contract.pharmacy_id
		INNER JOIN pharmaceutical ON pharmaceutical.pharmaceutical_id = contract.pharmaceutical_id
		WHERE contract.contract_id = :contract_id");
	$stmt->bindValue(':contract_id', $contract_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all drugs related to the contract
function getRelatedDrugs($contract_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT drug.*, pharmacy.name AS pharmacy_name, pharmaceutical.name AS pharmaceutical_name
		FROM drug
		JOIN contract ON drug.contract_id = contract.contract_id
		JOIN pharmacy ON contract.pharmacy_id = pharmacy.pharmacy_id
		JOIN pharmaceutical ON contract.pharmaceutical_id = pharmaceutical.pharmaceutical_id
		WHERE drug.contract_id = :contract_id");
	$stmt->bindValue(':contract_id', $contract_id, PDO::PARAM_INT);
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

// Get the contract ID from the URL parameter
if (isset($_GET['contract_id'])) {
	$contract_id = $_GET['contract_id'];
} else {
	// Redirect if no contract ID is provided
	header('Location: ../errors/not_found.php');
	exit;
}

// Get contract details
$contract_details = getContractDetails($contract_id);

// Get related drugs for the contract
$related_drugs = getRelatedDrugs($contract_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Profile</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Moment.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
    <div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
		<div class="max-w-full mx-auto" style = "margin:0 70px;">
		    <!-- Display contract details here -->
		    <h1 class="text-3xl font-semibold text-gray-800">Contract Profile #ID <?php echo $contract_details['contract_id']?></h1>
		    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $contract_details['pharmacy_name'] . ' - ' . $contract_details['pharmaceutical_name']; ?></h2>
		    <p class="text-sm text-gray-600">Start Date: <?php echo (new DateTime($contract_details['start_date']))->format('Y-m-d'); ?></p>
		    <p class="text-sm text-gray-600">End Date: <?php echo (new DateTime($contract_details['end_date']))->format('Y-m-d'); ?></p>

		    <!-- Table of Related Drugs -->
		    <?php if (!empty($related_drugs)) : ?>
			<div class="mt-10">
			    <h2 class="text-xl font-semibold text-gray-800">Related Drugs</h2>
			    <table class="mt-4 w-full border-collapse">
				<thead>
				    <tr>
					<th class="px-4 py-2">Drug ID</th>
					<th class="px-4 py-2">Scientific Name</th>
					<th class="px-4 py-2">Trade Name</th>
					<th class="px-4 py-2">Formula</th>
					<th class="px-4 py-2">Form</th>
					<th class="px-4 py-2">Expiry Date</th>
					<th class="px-4 py-2">Manufacturing Date</th>
					<th class="px-4 py-2">Amount</th>
					<th class="px-4 py-2">Actions</th>
				    </tr>
				</thead>
				<tbody>
				    <?php foreach ($related_drugs as $drug) : ?>
					<tr>
					    <td class="border px-4 py-2"><?php echo $drug['drug_id']; ?></td>
					    <td class="border px-4 py-2"><?php echo $drug['scientific_name']; ?></td>
					    <td class="border px-4 py-2"><?php echo $drug['trade_name']; ?></td>
					    <td class="border px-4 py-2"><?php echo $drug['formula']; ?></td>
					    <td class="border px-4 py-2"><?php echo $drug['form']; ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($contract_details['end_date']))->format('Y-m-d'); ?></td>
					    <td class="border px-4 py-2"><?php echo (new DateTime($contract_details['end_date']))->format('Y-m-d'); ?></td>
					    <td class="border px-4 py-2"><?php echo $drug['amount']; ?></td>
					    <td class="border px-4 py-2">
						<a href="../updates/update_drug.php?drug_id=<?php echo $drug['drug_id']; ?>" class="text-blue-500">Edit</a>
					    </td>
					</tr>
				    <?php endforeach; ?>
				</tbody>
			    </table>
			</div>
		    <?php endif; ?>

		    <!-- Add New Drug Button -->
		    <div class="mt-4">
			<a href="../registration/add_drug.php?contract_id=<?php echo $contract_details['contract_id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
			    Add New Drug
			</a>
		    </div>
		</div>
    <?php echo $footer; ?>
</body>

</html>
