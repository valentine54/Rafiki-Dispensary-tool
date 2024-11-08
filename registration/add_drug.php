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

// Function to handle drug addition and insert drug into the database
function addDrug($contract_id, $scientific_name, $trade_name, $formula, $form, $expiry_date, $manufacturing_date, $amount)
{
	global $pdo;

	try {
		$stmt = $pdo->prepare("INSERT INTO drug (contract_id, scientific_name, trade_name, formula, form, expiry_date, manufacturing_date, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$contract_id, $scientific_name, $trade_name, $formula, $form, $expiry_date, $manufacturing_date, $amount]);
		return true;
	} catch (PDOException $e) {
		// Handle any errors that may occur during drug addition
		return false;
	}
}

// Check if the user is logged in and is allowed (administrator or supervisor)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'administrator' && $_SESSION['user'] !== 'supervisor')) {
	header("Location: ../errors/not_allowed.php");
	exit();
}

// Check if the contract_id is provided in the URL
if (!isset($_GET['contract_id'])) {
	$error_message = "Please include the contract_id in the URL.";
}

// Get all possible drug forms to populate the select field
$drug_forms = getAllDrugForms();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	// Process the form data
	$contract_id = $_POST["contract_id"];
	$scientific_name = $_POST["scientific_name"];
	$trade_name = $_POST["trade_name"];
	$formula = $_POST["formula"];
	$form = $_POST["form"];
	$expiry_date = $_POST["expiry_date"];
	$manufacturing_date = $_POST["manufacturing_date"];
	$amount = $_POST["amount"];

	// Perform basic form validation
	if (empty($contract_id) || empty($scientific_name) || empty($trade_name) || empty($formula) || empty($form) || empty($expiry_date) || empty($manufacturing_date) || empty($amount)) {
		$error_message = "All fields are required.";
	} else {
		// Attempt to add the drug
		if (addDrug($contract_id, $scientific_name, $trade_name, $formula, $form, $expiry_date, $manufacturing_date, $amount)) {
			$success_message = "Drug added successfully.";
			header('Location: ../profiles/contract_profile.php?contract_id=' . $contract_id);
			exit;
		} else {
			$error_message = "An error occurred while adding the drug. Please try again later.";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drug</title>
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
	<h2 class="mb-4 text-2xl font-bold">Add Drug</h2>
	<?php if (isset($error_message)) : ?>
	    <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
	<?php endif; ?>
	<?php if (isset($success_message)) : ?>
	    <div class="mb-4 p-3 bg-green-200 text-green-700 rounded"><?php echo $success_message; ?></div>
	<?php endif; ?>
	<?php if (!isset($_GET['contract_id'])) : ?>
	    <div class="mb-4 p-3 bg-yellow-200 text-yellow-700 rounded">Please include the contract_id in the URL.</div>
	<?php endif; ?>
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="contract_id">Contract ID:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="contract_id" type="text" name="contract_id" value="<?php echo $_GET['contract_id'] ?? ''; ?>" readonly>
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="scientific_name">Scientific Name:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="scientific_name" type="text" name="scientific_name" placeholder="Enter scientific name">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="trade_name">Trade Name:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="trade_name" type="text" name="trade_name" placeholder="Enter trade name">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="formula">Formula:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="formula" type="text" name="formula" placeholder="Enter formula">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="form">Form:</label>
		<select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form" name="form">
		    <option value="" disabled selected>Select a form</option>
		    <?php foreach ($drug_forms as $drug_form) : ?>
			<option value="<?php echo $drug_form; ?>"><?php echo $drug_form; ?></option>
		    <?php endforeach; ?>
		</select>
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="expiry_date">Expiry Date:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="expiry_date" type="date" name="expiry_date">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="manufacturing_date">Manufacturing Date:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="manufacturing_date" type="date" name="manufacturing_date">
	    </div>
	    <div class="mb-4">
		<label class="block text-gray-700 text-sm font-bold mb-2" for="amount">Amount:</label>
		<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="amount" type="number" name="amount" placeholder="Enter amount">
	    </div>
	    <div class="flex items-center justify-between">
		<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Add Drug</button>
	    </div>
	</form>
    </div>
</div>
<?php echo $footer; ?>
</body>

</html>
