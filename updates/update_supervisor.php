<?php
// Assuming the database connection variables are available in ../database_connection.php
require_once '../database_connection.php';

// Function to get supervisor details by supervisor_id
function getSupervisorDetails($supervisor_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM supervisor WHERE supervisor_id = ?");
    $stmt->execute([$supervisor_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all pharmaceuticals
function getAllPharmaceuticals()
{
    global $pdo;

    $stmt = $pdo->query("SELECT * FROM pharmaceutical");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to update supervisor details in the database
function updateSupervisorDetails($supervisor_id, $pharmaceutical_id, $name, $email, $mobile_number, $password_hash)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE supervisor SET pharmaceutical_id = ?, name = ?, email = ?, mobile_number = ?, password_hash = ? WHERE supervisor_id = ?");
        $stmt->execute([$pharmaceutical_id, $name, $email, $mobile_number, $password_hash, $supervisor_id]);
        return true;
    } catch (PDOException $e) {
        // Handle any errors that may occur during supervisor details update
        return false;
    }
}

// Check if the user is logged in and is allowed (supervisor or administrator)
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user'] !== 'supervisor' && $_SESSION['user'] !== 'administrator')) {
    header("Location: ../errors/not_allowed.php");
    exit();
}

// Check if the supervisor_id is provided in the URL
if (!isset($_GET['supervisor_id'])) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get supervisor details by supervisor_id
$supervisor_id = $_GET['supervisor_id'];
$supervisor_details = getSupervisorDetails($supervisor_id);

// Check if the supervisor exists
if (!$supervisor_details) {
    header("Location: ../errors/not_found.php");
    exit();
}

// Get all pharmaceuticals
$pharmaceuticals = getAllPharmaceuticals();

// Set default values for the form fields
$pharmaceutical_id = $supervisor_details['pharmaceutical_id'];
$name = $supervisor_details['name'];
$email = $supervisor_details['email'];
$mobile_number = $supervisor_details['mobile_number'];
$password = $supervisor_details['password_hash'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form data
    $pharmaceutical_id = $_POST["pharmaceutical_id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $password = $_POST["password"];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Perform basic form validation
    if (empty($pharmaceutical_id) || empty($name) || empty($email) || empty($mobile_number) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Attempt to update supervisor details
        if (updateSupervisorDetails($supervisor_id, $pharmaceutical_id, $name, $email, $mobile_number, $password_hash)) {
            header("Location: ../profiles/supervisor_profile.php?supervisor_id=" . $supervisor_id);
            exit();
        } else {
            $error_message = "An error occurred while updating supervisor details. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supervisor Details</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb700 text-sm font-bold mb-2" for="mobile_number">Mobile Number:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="mobile_number" type="text" name="mobile_number" value="<?php echo $mobile_number; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="text" name="password" value="<?php echo $password; ?>">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Update Details</button>
            </div>
        </form>
    </div>
</body>

</html>
