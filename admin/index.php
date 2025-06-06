<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'admin') {
  header('Location: ../login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang=\"pt\">
<head>
  <meta charset=\"UTF-8\">
  <title>Administração</title>
</head>
<body>
  <h2>Painel do Administrador</h2>
  <p>Bem-vindo, <?php echo $_SESSION['user']['nome']; ?>!</p>
  <ul>
    <li><a href=\"gerir_eventos.php\">Gerir Eventos</a></li>
    <li><a href=\"gerir_palestras.php\">Gerir Palestras</a></li>
    <li><a href=\"logout.php\">Sair</a></li>
  </ul>
</body>
</html>
