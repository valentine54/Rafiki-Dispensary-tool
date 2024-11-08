<?php
require_once '../database_connection.php';

// Check if the user is logged in and is an administrator
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'administrator') {
	header("Location: ../errors/not_allowed.php");
	exit();
}

// Fetch the administrator details
function getAdministratorDetails()
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM administrator WHERE administrator_id = :admin_id");
	$stmt->bindValue(':admin_id', $_SESSION['user_id'], PDO::PARAM_INT);
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

// Get the administrator details
$administrator = getAdministratorDetails();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
    <div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
	<div class="relative py-3 sm:max-w-xl sm:mx-auto">
	    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
	    <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
		<div class="max-w-md mx-auto">
		    <div>
			<h1 class="text-4xl font-semibold text-gray-800">Welcome, <?php echo $administrator['name']; ?></h1>
			<p class="mt-4 text-gray-500">You are logged in as an administrator.</p>
		    </div>
		    <div class="mt-8">
			<h2 class="text-2xl font-semibold text-gray-800">Profile Information</h2>
			<div class="mt-6">
			    <div class="flex items-center justify-between">
				<span class="text-gray-500">Administrator ID:</span>
				<span class="text-gray-700 font-semibold"><?php echo $administrator['administrator_id']; ?></span>
			    </div>
			    <div class="flex items-center justify-between mt-4">
				<span class="text-gray-500">Email:</span>
				<span class="text-gray-700 font-semibold"><?php echo $administrator['email']; ?></span>
			    </div>
			    <div class="flex items-center justify-between mt-4">
				<span class="text-gray-500">Mobile Number:</span>
				<span class="text-gray-700 font-semibold"><?php echo $administrator['mobile_number']; ?></span>
			    </div>
			</div>
		    </div>
		    <div class="mt-10">
			<h2 class="text-2xl font-semibold text-gray-800">Registrations</h2>
			<ul class="mt-4 space-y-4">
			    <li>
				<a class="text-blue-600 hover:underline" href="../registration/register_patient.php">Register Patient</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../registration/register_doctor.php">Register Doctor</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../registration/register_pharmacy.php">Register Pharmacy</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../registration/register_pharmaceutical.php">Register Pharmaceutical</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../registration/register_pharmacist.php">Register Pharmacist</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../registration/register_supervisor.php">Register Supervisor</a>
			    </li>
			</ul>
		    </div>
		    <div class="mt-10">
			<h2 class="text-2xl font-semibold text-gray-800">Views</h2>
			<ul class="mt-4 space-y-4">
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_patients.php">View Patients</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_doctors.php">View Doctors</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_pharmacies.php">View Pharmacies</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_pharmaceuticals.php">View Pharmaceuticals</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_supervisors.php">View Supervisors</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_pharmacists.php">View Pharmacists</a>
			    </li>
			    <li>
				<a class="text-blue-600 hover:underline" href="../views/view_drugs.php">View Drugs</a>
			    </li>
			</ul>
		    </div>
		</div>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>
</body>

</html>
