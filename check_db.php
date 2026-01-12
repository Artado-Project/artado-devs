<?php
require_once 'includes/database.php';
echo "--- PROJECTS TABLE ---\n";
$stmt = $db->query("DESCRIBE projects");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "\n--- USERS TABLE ---\n";
$stmt = $db->query("DESCRIBE users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
