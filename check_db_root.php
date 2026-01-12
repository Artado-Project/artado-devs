<?php
$host = "localhost";
$dbname = "artadodevs";
$username = "root";
$password = "";

try {
  $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  echo "--- PROJECTS TABLE ---\n";
  $stmt = $db->query("DESCRIBE projects");
  print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>
