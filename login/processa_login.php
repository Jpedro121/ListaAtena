<?php
session_start();
require 'includes/db.php'; // Inclui a conexão

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM utilizadores WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
  $user = $res->fetch_assoc();
  if (password_verify($senha, $user['senha'])) {
    $_SESSION['user'] = $user;
    header('Location: ' . ($user['tipo'] === 'admin' ? 'admin/index.php' : 'index.php'));
    exit;
  }
}
echo "Email ou senha inválidos.";
?>
