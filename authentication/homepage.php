<?php
session_start();
$isLoggedIn = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rafiki Drug Dispenser</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
    <!-- Hero Section -->
    <div class="bg-blue-400 py-16 px-8 text-white">
	<div class="container mx-auto">
	    <h1 class="text-4xl font-bold mb-4">Welcome to Rafiki Drug Dispenser</h1>
	    <p class="text-lg mb-8">Your Trusted Partner in Drug Dispensing</p>
	    <a href="../registration/register_patient.php" class="bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800">Join Us
		Today</a>
	</div>
    </div>

    <!-- About Us Section -->
    <section class="py-10 px-8">
	<div class="container mx-auto">
	    <h2 class="text-3xl font-semibold mb-4">Who We Are</h2>
	    <p class="text-lg mb-8">
		Rafiki Drug Dispenser is a leading drug dispensing system designed to streamline and optimize the
		prescription and medication process for healthcare professionals and patients. Our platform caters to
		various entities, including pharmacies, pharmaceuticals, doctors, and patients, offering a seamless and
		secure experience for all users.
	    </p>
	    <h2 class="text-3xl font-semibold mb-4">What We Do</h2>
	    <p class="text-lg mb-8">
		We provide an advanced drug dispensing system that empowers healthcare professionals to manage
		prescriptions efficiently. Our platform facilitates seamless communication between doctors, pharmacists,
		and patients, ensuring accurate and timely delivery of medications. Patients can access their
		prescriptions and manage their health with ease.
	    </p>
	    <h2 class="text-3xl font-semibold mb-4">Why Join Us</h2>
	    <p class="text-lg mb-8">
		Joining Rafiki Drug Dispenser opens up a world of possibilities for pharmacies, pharmaceuticals,
		doctors, NGOs, and patients. Our platform offers numerous benefits, including improved patient care,
		streamlined workflows, secure data management, and enhanced collaboration among healthcare
		professionals. By becoming a part of our network, you join a community committed to delivering
		high-quality healthcare services.
	    </p>
	    <a href="../registration/register_patient.php" class="bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800">Join Us Today</a>
	</div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 px-8 bg-blue-500 text-white text-center">
	<h2 class="text-3xl font-semibold mb-4">Join Rafiki Drug Dispenser Today!</h2>
	<p class="text-lg mb-8">
	    Are you a pharmacy, pharmaceutical, doctor, NGO, or patient looking to optimize your drug dispensing
	    process? Join us today and experience the benefits of our advanced drug dispensing system. Let's work
	    together to enhance patient care and streamline medication management.
	</p>
	<a href="../authentication/about_us.php" class="bg-white text-blue-500 px-6 py-3 rounded-lg font-semibold hover:bg-blue-200">Learn More</a>
    </section>

    <!-- Footer Section -->
    <?php echo $footer; ?>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>

</html>
