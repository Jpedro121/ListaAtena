<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'listaatena';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Erro de conexão: " . $conn->connect_error);
}

// Define charset para evitar problemas de encoding
$conn->set_charset("utf8mb4");
?>
