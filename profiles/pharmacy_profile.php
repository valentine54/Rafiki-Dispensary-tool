<?php
session_start();
require_once '../database_connection.php';

// Function to get pharmacy details
function getPharmacyDetails($pharmacy_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM pharmacy WHERE pharmacy_id = :pharmacy_id");
	$stmt->bindValue(':pharmacy_id', $pharmacy_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get related pharmacists for the pharmacy
function getRelatedPharmacists($pharmacy_id)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT pharmacist.*, pharmacy.name AS pharmacy_name
		FROM pharmacist
		JOIN pharmacy ON pharmacist.pharmacy_id = pharmacy.pharmacy_id
		WHERE pharmacist.pharmacy_id = :pharmacy_id");
	$stmt->bindValue(':pharmacy_id', $pharmacy_id, PDO::PARAM_INT);
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

// Get the pharmacy ID from the URL parameter
if (isset($_GET['pharmacy_id'])) {
	$pharmacy_id = $_GET['pharmacy_id'];
} else {
	// Redirect if no pharmacy ID is provided
	header('Location: ../errors/not_found.php');
	exit;
}

// Get pharmacy details
$pharmacy_details = getPharmacyDetails($pharmacy_id);

// Get related pharmacists for the pharmacy
$related_pharmacists = getRelatedPharmacists($pharmacy_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Profile</title>
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
		    <!-- Display pharmacy details here -->
		    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $pharmacy_details['name']; ?></h2>
		    <p class="text-sm text-gray-600">Location: <?php echo $pharmacy_details['location']; ?></p>
		    <p class="text-sm text-gray-600">Email: <?php echo $pharmacy_details['email']; ?></p>
		    <p class="text-sm text-gray-600">Mobile Number: <?php echo $pharmacy_details['mobile_number']; ?></p>

		    <!-- Table of Registered Pharmacists -->
		    <?php if (!empty($related_pharmacists)) : ?>
			<div class="mt-10">
			    <h2 class="text-xl font-semibold text-gray-800">Registered Pharmacists</h2>
			    <table class="mt-4 w-full border-collapse">
				<thead>
				    <tr>
					<th class="px-4 py-2">Pharmacist ID</th>
					<th class="px-4 py-2">Name</th>
					<th class="px-4 py-2">Email</th>
					<th class="px-4 py-2">Mobile Number</th>
				    </tr>
				</thead>
				<tbody>
				    <?php foreach ($related_pharmacists as $pharmacist) : ?>
					<tr>
					    <td class="border px-4 py-2"><?php echo $pharmacist['pharmacist_id']; ?></td>
					    <td class="border px-4 py-2"><a href="../profiles/pharmacist_profile.php?pharmacist_id=<?php echo $pharmacist['pharmacist_id']; ?>" class="text-blue-500"><?php echo $pharmacist['name']; ?></a></td>
					    <td class="border px-4 py-2"><?php echo $pharmacist['email']; ?></td>
					    <td class="border px-4 py-2"><?php echo $pharmacist['mobile_number']; ?></td>
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
