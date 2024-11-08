<?php
$database_host = "localhost";
$database_user = "root";
$database_password = "MySQLXXX-123a8910";
$database_name = "dispenser";

try {
	$pdo = new PDO("mysql:host=$database_host;dbname=$database_name", $database_user, $database_password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Database connection failed: " . $e->getMessage());
}
?>
