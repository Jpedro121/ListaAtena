<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Registo</title>
  <?php include '../includes/head.html'; ?>
</head>
<body>
  <?php include '../includes/header.php'; ?>
  <main>
    <h2>Registar Conta</h2>
    <form action="processa_registo.php" method="POST">
      <label>Nome:</label><br>
      <input type="text" name="nome" required><br><br>

      <label>Email (ESJS):</label><br>
      <input type="email" name="email" required><br><br>

      <label>Senha:</label><br>
      <input type="password" name="senha" required><br><br>

      <button type="submit">Criar Conta</button>
    </form>
  </main>
</body>
</html>
