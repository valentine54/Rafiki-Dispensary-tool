<?php
require_once '../database_connection.php';

// Function to get total number of pharmaceuticals
function getTotalPharmaceuticals()
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT COUNT(pharmaceutical_id) as total_pharmaceuticals FROM pharmaceutical");
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC)['total_pharmaceuticals'];
}

// Function to get pharmaceuticals with pagination
function getPharmaceuticalsWithPagination($limit, $offset)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM pharmaceutical LIMIT :limit OFFSET :offset");
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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



// Check if the user is logged in and is allowed (administrator)
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'administrator') {
	header("Location: ../errors/not_allowed.php");
	exit();
}

// Set the number of records per page
$records_per_page = 10;

// Get the total number of pharmaceuticals
$total_pharmaceuticals = getTotalPharmaceuticals();

// Calculate total number of pages
$total_pages = ceil($total_pharmaceuticals / $records_per_page);

// Get the current page from the URL
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

// Ensure current page is within valid range
$current_page = max(1, $current_page);
$current_page = min($current_page, $total_pages);

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $records_per_page;

// Get pharmaceuticals with pagination
$pharmaceuticals = getPharmaceuticalsWithPagination($records_per_page, $offset);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pharmaceuticals</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-md rounded p-4 w-full lg:w-2/3">
	<h2 class="mb-4 text-2xl font-bold">All Registered Pharmaceuticals</h2>
	<?php if (empty($pharmaceuticals)) : ?>
	    <div class="text-red-500 font-bold mb-4">No pharmaceuticals found.</div>
	<?php else : ?>
	    <div class="overflow-x-auto">
		<table class="table-auto w-full border-collapse">
		    <thead>
			<tr>
			    <th class="px-4 py-2">Pharmaceutical ID</th>
			    <th class="px-4 py-2">Name</th>
			    <th class="px-4 py-2">Location</th>
			    <th class="px-4 py-2">Email</th>
			    <th class="px-4 py-2">Mobile Number</th>
			</tr>
		    </thead>
		    <tbody>
			<?php foreach ($pharmaceuticals as $pharmaceutical) : ?>
			    <tr>
				<td class="border px-4 py-2"><?php echo $pharmaceutical['pharmaceutical_id']; ?></td>
				<td class="border px-4 py-2"><?php echo $pharmaceutical['name']; ?></td>
				<td class="border px-4 py-2"><?php echo $pharmaceutical['location']; ?></td>
				<td class="border px-4 py-2"><?php echo $pharmaceutical['email']; ?></td>
				<td class="border px-4 py-2"><?php echo $pharmaceutical['mobile_number']; ?></td>
				<td class="border px-4 py-2"><a class="text-blue-500 hover:underline" href="../profiles/pharmaceutical_profile.php?pharmaceutical_id=<?php echo $pharmaceutical['pharmaceutical_id']; ?>">View Profile</a></td>
			    </tr>
			<?php endforeach; ?>
		    </tbody>
		</table>
	    </div>
	    <!-- Pagination links -->
	    <div class="flex items-center justify-center mt-4">
		<?php if ($current_page > 1) : ?>
		    <a class="px-3 py-2 bg-blue-500 text-white rounded-lg mr-2" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
		<?php endif; ?>
		<?php if ($current_page < $total_pages) : ?>
		    <a class="px-3 py-2 bg-blue-500 text-white rounded-lg" href="?page=<?php echo $current_page + 1; ?>">Next</a>
		<?php endif; ?>
	    </div>
	<?php endif; ?>
    </div>
</div>
<?php echo $footer; ?>
</body>

</html>
