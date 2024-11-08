<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<?php
require_once('../navigation_bar.php');
echo $navigationBar;
?>
    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-semibold text-center mb-6">About Our Drug Dispensing System</h1>
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <p class="text-lg mb-4">
                Our drug dispensing system is an advanced platform designed to manage and facilitate the process of
                prescribing and dispensing drugs to patients. It caters to various user roles, including administrators,
                patients, doctors, pharmacists, and supervisors, each with their specific set of permissions and
                functionalities.
            </p>
            <p class="text-lg mb-4">
                With our system, administrators can manage user registrations, view user profiles, and control system
                settings. Patients can view and update their personal details, as well as access their prescriptions and
                assigned doctors. Doctors can manage patient assignments and prescribe drugs. Pharmacists can manage drug
                prescriptions, and supervisors can oversee pharmaceutical contracts and associated data.
            </p>
            <p class="text-lg mb-4">
                Throughout the development of this system, we have utilized the powerful MySQL database to store user
                information and manage the relationships between various entities. We have implemented user
                authentication, ensuring secure access to the system's functionalities. Additionally, we have utilized
                PHP and PDO (PHP Data Objects) for database interactions, enhancing security and efficiency.
            </p>
            <p class="text-lg">
                Our user-friendly and intuitive interface, powered by Tailwind CSS, enhances the user experience and
                makes navigation seamless. We have incorporated pagination to manage large datasets efficiently and used
                Moment.js to handle dates and times, ensuring accurate and user-friendly date representations.
            </p>
        </div>
    </div>
    <?php echo $footer; ?>
</body>

</html>
