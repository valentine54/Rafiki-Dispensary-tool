<?php
session_start();
require_once '../database_connection.php';

// Function to get pharmaceutical details
function getPharmaceuticalDetails($pharmaceutical_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM pharmaceutical WHERE pharmaceutical_id = :pharmaceutical_id");
	$stmt->bindValue(':pharmaceutical_id', $pharmaceutical_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get related supervisors for the pharmaceutical
function getRelatedSupervisors($pharmaceutical_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT supervisor.*, pharmaceutical.name AS pharmaceutical_name
		FROM supervisor
		JOIN pharmaceutical ON supervisor.pharmaceutical_id = pharmaceutical.pharmaceutical_id
		WHERE supervisor.pharmaceutical_id = :pharmaceutical_id");
	$stmt->bindValue(':pharmaceutical_id', $pharmaceutical_id, PDO::PARAM_INT);
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

// Get the pharmaceutical ID from the URL parameter
if (isset($_GET['pharmaceutical_id'])) {
	$pharmaceutical_id = $_GET['pharmaceutical_id'];
} else {
	// Redirect if no pharmaceutical ID is provided
	header('Location: ../errors/not_found.php');
	exit;
}

// Get pharmaceutical details
$pharmaceutical_details = getPharmaceuticalDetails($pharmaceutical_id);

// Get related supervisors for the pharmaceutical
$related_supervisors = getRelatedSupervisors($pharmaceutical_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmaceutical Profile</title>
    <!-- Tailwind CSS -->
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
		    <!-- Display pharmaceutical details here -->
		    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $pharmaceutical_details['name']; ?></h2>
		    <p class="text-sm text-gray-600">Location: <?php echo $pharmaceutical_details['location']; ?></p>
		    <p class="text-sm text-gray-600">Email: <?php echo $pharmaceutical_details['email']; ?></p>
		    <p class="text-sm text-gray-600">Mobile Number: <?php echo $pharmaceutical_details['mobile_number']; ?></p>

		    <!-- Table of Registered Supervisors -->
		    <?php if (!empty($related_supervisors)) : ?>
			<div class="mt-10">
			    <h2 class="text-xl font-semibold text-gray-800">Registered Supervisors</h2>
			    <table class="mt-4 w-full border-collapse">
				<thead>
				    <tr>
					<th class="px-4 py-2">Supervisor ID</th>
					<th class="px-4 py-2">Name</th>
					<th class="px-4 py-2">Email</th>
					<th class="px-4 py-2">Mobile Number</th>
				    </tr>
				</thead>
				<tbody>
				    <?php foreach ($related_supervisors as $supervisor) : ?>
					<tr>
					    <td class="border px-4 py-2"><?php echo $supervisor['supervisor_id']; ?></td>
					    <td class="border px-4 py-2"><a href="../profiles/supervisor_profile.php?supervisor_id=<?php echo $supervisor['supervisor_id']; ?>" class="text-blue-500"><?php echo $supervisor['name']; ?></a></td>
					    <td class="border px-4 py-2"><?php echo $supervisor['email']; ?></td>
					    <td class="border px-4 py-2"><?php echo $supervisor['mobile_number']; ?></td>
					</tr>
				    <?php endforeach; ?>
				</tbody>
			    </table>
			</div>
		    <?php endif; ?>
		</div>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>
</body>

</html>
