<?php
$isLoggedIn = isset($_SESSION['user']);
if ($isLoggedIn) {
	$user = $_SESSION['user'];
	$user_id = $_SESSION['user_id'];
}
$navigationBar = '
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <nav class="bg-blue-500 text-white py-4 px-8 flex justify-between items-center">
	<div class="flex items-center">
	    <span class="text-xl font-semibold mr-4">Rafiki Drug Dispenser</span>
	    <div class="hidden md:flex space-x-4">
		<a href="../authentication/homepage.php" class="hover:underline">Home</a>
		<a href="../authentication/about_us.php" class="hover:underline">About Us</a>
		<a href="../authentication/contacts.php" class="hover:underline">Contacts</a>
	    </div>
	</div>
	<div class="flex items-center space-x-4">
';
if (!$isLoggedIn) {
	$navigationBar .= '
		<a href="../authentication/login.php" class="hover:underline">Sign In</a>
		<a href="../registration/register_patient.php" class="hover:underline">Sign Up</a>';
} else {
	$navigationBar .= '<span class="text-lg font-semibold">
		<a href = "../profiles/' . $user . '_profile.php?' . $user . '_id=' . $user_id. '">' . $user . ' Home
		</a>
		</span>
		<a href="../authentication/logout.php" class="hover:underline">Sign Out</a>';
}
$navigationBar .= '
	    <a href="#" class="text-2xl">
		<i class="fab fa-facebook"></i>
	    </a>
	    <a href="#" class="text-2xl">
		<i class="fab fa-twitter"></i>
	    </a>
	    <a href="#" class="text-2xl">
		<i class="fab fa-instagram"></i>
	    </a>
	</div>
    </nav>
';

$footer = '
    <footer class="bg-gray-800 text-white py-8 px-8">
	<div class="container mx-auto flex justify-between">
	    <div class="w-1/3">
		<h3 class="text-xl font-semibold mb-4">Rafiki Drug Dispenser</h3>
		<p class="text-sm">Address: 1234 Any Street, Nakuru, Kenya</p>
		<p class="text-sm">Phone: +254 123 456 789</p>
		<p class="text-sm">Email: info@rafikidrugdispenser.com</p>
	    </div>
	    <div class="w-1/3">
		<h3 class="text-xl font-semibold mb-4">Quick Links</h3>
		<ul class="text-sm">
		    <li><a href="../authentication/homepage.php" class="hover:underline">Home</a></li>
		    <li><a href="../authentication/about_us.php" class="hover:underline">About Us</a></li>
		    <li><a href="../authentication/contacts.php" class="hover:underline">Contacts</a></li>';
if ($isLoggedIn) {
	$footer .= '
		    <li><a href="../profiles/' . $user . '_profile.php?' . $user . '_id=' . $user_id . '" class="hover:underline">Profile</a></li>';
}
$footer .= '
		</ul>
	    </div>
	    <div class="w-1/3">
		<h3 class="text-xl font-semibold mb-4">Our Services</h3>
		<ul class="text-sm">
		    <li>Streamlined Prescription Management</li>
		    <li>Secure Medication Dispensing</li>
		    <li>Enhanced Collaboration Among Healthcare Professionals</li>
		    <li>Improved Patient Care</li>
		</ul>
	    </div>
	</div>
    </footer>';
?>
