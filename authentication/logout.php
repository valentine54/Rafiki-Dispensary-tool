<?php
// Start the session
session_start();

// Destroy all session data
session_destroy();

// Redirect to the logout script
header('Location: ../authentication/login.php');
exit;
?>
