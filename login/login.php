<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Login</title>
<?php include '../includes/head.html'; ?>

<body>
<?php include '../includes/header.php'; ?>
  <main>
    <h2>Login</h2>
    
    <form action="processa_login.php" method="POST">
      <label>Email:</label><br>
      <input type="email" name="email" required><br>

      <label>Senha:</label><br>
      <input type="password" name="senha" required><br><br>

      <button type="submit">Entrar</button>
    </form>

    <div class="signup-link">
      <p>Não tens conta? <a class="signup-link" href="registar.php">Cria uma conta</a></p>
    </div>
    <div class="signup-link">
      <p><a class="signup-link" href="forgot_password.php">Esqueceste-te da palavra-passe?</a></p>
    </div>
  </main>
</body>
</html>
