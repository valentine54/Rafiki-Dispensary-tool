<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get drug details by drug_id
function getDrugDetails($drug_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM drug WHERE drug_id = ?");
    $stmt->execute([$drug_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to update drug details in the database
function updateDrugDetails($drug_id, $scientific_name, $trade_name, $formula, $form, $expiry_date, $manufacturing_date, $amount)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE drug SET scientific_name = ?, trade_name = ?, formula = ?, form = ?, expiry_date = ?, manufacturing_date = ?, amount = ? WHERE drug_id = ?");
        $stmt->execute([$scientific_name, $trade_name, $formula, $form, $expiry_date, $manufacturing_date, $amount, $drug_id]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during drug details update
        return false;
    }
}

// Check if the user is logged in and is allowed (supervisor, pharmacist, or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'supervisor' && $_SESSION['user'] !== 'pharmacist' && $_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the drug_id is provided in the URL
if (!isset($_GET['drug_id'])) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get drug details by drug_id
$drug_id = $_GET['drug_id'];
$drug_details = getDrugDetails($drug_id);

// Check if the drug exists
if (!$drug_details) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Set default values for the form fields
$scientific_name = $drug_details['scientific_name'];
$trade_name = $drug_details['trade_name'];
$formula = $drug_details['formula'];
$form = $drug_details['form'];
$expiry_date = $drug_details['expiry_date'];
$manufacturing_date = $drug_details['manufacturing_date'];
$amount = $drug_details['amount'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $scientific_name = $_POST["scientific_name"];
    $trade_name = $_POST["trade_name"];
    $formula = $_POST["formula"];
    $form = $_POST["form"];
    $expiry_date = $_POST["expiry_date"];
    $manufacturing_date = $_POST["manufacturing_date"];
    $amount = $_POST["amount"];

    // Perform basic form validation
    if (empty($scientific_name) || empty($trade_name) || empty($formula) || empty($form) || empty($expiry_date) || empty($manufacturing_date) || empty($amount)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to update drug details
        if (updateDrugDetails($drug_id, $scientific_name, $trade_name, $formula, $form, $expiry_date, $manufacturing_date, $amount)) {
            header("Location: ../profiles/contract_profile.php?contract_id=" . $drug_details['contract_id']);
            exit();
        } else {
            $error_message = "An error occurred while updating drug details. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Drug Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Edit Drug Details</h2>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?drug_id=" . $drug_id; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="scientific_name">Scientific Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="scientific_name" type="text" name="scientific_name" value="<?php echo $scientific_name; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="trade_name">Trade Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="trade_name" type="text" name="trade_name" value="<?php echo $trade_name; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="formula">Formula:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="formula" type="text" name="formula" value="<?php echo $formula; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="form">Form:</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form" name="form">
                    <option value="Tablet" <?php if ($form === 'Tablet') echo 'selected'; ?>>Tablet</option>
                    <option value="Capsule" <?php if ($form === 'Capsule') echo 'selected'; ?>>Capsule</option>
                    <option value="Liquid" <?php if ($form === 'Liquid') echo 'selected'; ?>>Liquid</option>
                    <option value="Cream" <?php if ($form === 'Cream') echo 'selected'; ?>>Cream</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="expiry_date">Expiry Date:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="expiry_date" type="date" name="expiry_date" value="<?php echo $expiry_date; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="manufacturing_date">Manufacturing Date:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="manufacturing_date" type="date" name="manufacturing_date" value="<?php echo $manufacturing_date; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">Amount:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="amount" type="number" name="amount" value="<?php echo $amount; ?>">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Update Details</button>
            </div>
        </form>
    </div>
</body>

</html>
