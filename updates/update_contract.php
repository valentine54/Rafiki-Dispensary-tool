<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get contract details by contract_id
function getContractDetails($contract_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM contract WHERE contract_id = ?");
    $stmt->execute([$contract_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to update contract details in the database
function updateContractDetails($contract_id, $start_date, $end_date)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE contract SET start_date = ?, end_date = ? WHERE contract_id = ?");
        $stmt->execute([$start_date, $end_date, $contract_id]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during contract details update
        return false;
    }
}

// Check if the user is logged in and is allowed (supervisor, pharmacist, or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'supervisor' && $_SESSION['user'] !== 'pharmacist' && $_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the contract_id is provided in the URL
if (!isset($_GET['contract_id'])) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get contract details by contract_id
$contract_id = $_GET['contract_id'];
$contract_details = getContractDetails($contract_id);

// Check if the contract exists
if (!$contract_details) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Set default values for the form fields
$start_date = $contract_details['start_date'];
$end_date = $contract_details['end_date'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

    // Perform basic form validation
    if (empty($start_date) || empty($end_date)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to update contract details
        if (updateContractDetails($contract_id, $start_date, $end_date)) {
            header("Location: ../profiles/contract_profile.php?contract_id=" . $contract_id);
            exit();
        } else {
            $error_message = "An error occurred while updating contract details. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contract Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="mb-4 text-2xl font-bold">Edit Contract Details</h2>
        <?php if (isset($error_message)) : ?>
            <div class="mb-4 p-3 bg-red-200 text-red-700 rounded"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?contract_id=" . $contract_id; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">Start Date:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="start_date" type="date" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">End Date:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="end_date" type="date" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Update Details</button>
            </div>
        </form>
    </div>
</body>

</html>
