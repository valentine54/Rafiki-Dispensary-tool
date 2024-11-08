<?php
require_once '../database_connection.php';

// Function to get total number of drugs
function getTotalDrugs()
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT COUNT(drug.drug_id) as total_drugs FROM drug
		JOIN contract ON drug.contract_id = contract.contract_id
		JOIN pharmaceutical ON contract.pharmaceutical_id = pharmaceutical.pharmaceutical_id
		JOIN pharmacy ON contract.pharmacy_id = pharmacy.pharmacy_id");
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC)['total_drugs'];
}

// Function to get drugs with pagination
function getDrugsWithPagination($limit, $offset)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT drug.drug_id, drug.scientific_name, drug.trade_name, drug.formula,
		drug.form, drug.expiry_date, drug.manufacturing_date, drug.amount,
		drug.contract_id, pharmaceutical.name as pharmaceutical_name, pharmacy.name as pharmacy_name
		FROM drug
		JOIN contract ON drug.contract_id = contract.contract_id
		JOIN pharmaceutical ON contract.pharmaceutical_id = pharmaceutical.pharmaceutical_id
		JOIN pharmacy ON contract.pharmacy_id = pharmacy.pharmacy_id
		LIMIT :limit OFFSET :offset");
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

// Get the total number of drugs
$total_drugs = getTotalDrugs();

// Calculate total number of pages
$total_pages = ceil($total_drugs / $records_per_page);

// Get the current page from the URL
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

// Ensure current page is within valid range
$current_page = max(1, $current_page);
$current_page = min($current_page, $total_pages);

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $records_per_page;

// Get drugs with pagination
$drugs = getDrugsWithPagination($records_per_page, $offset);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Drugs</title>
    <!-- Link to Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Link to Moment.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-md rounded p-4 w-full lg:w-2/3">
	<h2 class="mb-4 text-2xl font-bold">All Registered Drugs</h2>
	<?php if (empty($drugs)) : ?>
	    <div class="text-red-500 font-bold mb-4">No drugs found.</div>
	<?php else : ?>
	    <div class="overflow-x-auto">
		<table class="table-auto w-full border-collapse">
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
			    <th class="px-4 py-2">Pharmaceutical Name</th>
			    <th class="px-4 py-2">Pharmacy Name</th>
			    <th class="px-4 py-2">Action</th>
			</tr>
		    </thead>
		    <tbody>
			<?php foreach ($drugs as $drug) : ?>
			    <tr>
				<td class="border px-4 py-2"><?php echo $drug['drug_id']; ?></td>
				<td class="border px-4 py-2"><?php echo $drug['scientific_name']; ?></td>
				<td class="border px-4 py-2"><?php echo $drug['trade_name']; ?></td>
				<td class="border px-4 py-2"><?php echo $drug['formula']; ?></td>
				<td class="border px-4 py-2"><?php echo $drug['form']; ?></td>
				<td class="border px-4 py-2"><?php echo moment($drug['expiry_date'])->format('YYYY-MM-DD'); ?></td>
				<td class="border px-4 py-2"><?php echo moment($drug['manufacturing_date'])->format('YYYY-MM-DD'); ?></td>
				<td class="border px-4 py-2"><?php echo $drug['amount']; ?></td>
				<td class="border px-4 py-2">
				    <a class="text-blue-500 hover:underline" href="../profiles/pharmaceutical_profile.php?pharmaceutical_id=<?php echo $drug['contract_id']; ?>"><?php echo $drug['pharmaceutical_name']; ?></a>
				</td>
				<td class="border px-4 py-2">
				    <a class="text-blue-500 hover:underline" href="../profiles/pharmacy_profile.php?pharmacy_id=<?php echo $drug['contract_id']; ?>"><?php echo $drug['pharmacy_name']; ?></a>
				</td>
				<td class="border px-4 py-2">
				    <a class="text-blue-500 hover:underline" href="../updates/update_drug.php?drug_id=<?php echo $drug['drug_id']; ?>">Edit</a>
				</td>
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

