<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Registar</title>
  <?php include '../includes/head.html'; ?>
  <style>
    .email-warning {
      color: #d9534f;
      font-size: 0.9rem;
      margin-top: -15px;
      margin-bottom: 15px;
      display: none;
    }
    
    .email-success {
      color: #5cb85c;
      font-size: 0.9rem;
      margin-top: -15px;
      margin-bottom: 15px;
      display: none;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>
  <main>
    <h2>Registar</h2>
    
    <form action="processa_registo.php" method="POST" id="registerForm">
      <label>Nome:</label><br>
      <input type="text" name="usernome" required><br>

      <label>Email:</label><br> 
      <input type="email" name="email" id="emailInput" required>
      <div id="emailWarning" class="email-warning">
        Por favor, utilize um email @esjs-mafra.net
      </div>
      <div id="emailSuccess" class="email-success">
        Email válido!
      </div><br>

      <label>Palavra-Passe:</label><br>
      <input type="password" name="senha" required><br><br>

      <button type="submit" id="submitButton">Registar</button>
    </form>
  </main>

  <script>
    document.getElementById('emailInput').addEventListener('input', function() {
      const email = this.value;
      const emailWarning = document.getElementById('emailWarning');
      const emailSuccess = document.getElementById('emailSuccess');
      const submitButton = document.getElementById('submitButton');
      
      if (email.includes('@') && !email.endsWith('@esjs-mafra.net')) {
        emailWarning.style.display = 'block';
        emailSuccess.style.display = 'none';
        submitButton.disabled = true;
        submitButton.style.opacity = '0.5';
        submitButton.style.cursor = 'not-allowed';
      } else if (email.endsWith('@esjs-mafra.net')) {
        emailWarning.style.display = 'none';
        emailSuccess.style.display = 'block';
        submitButton.disabled = false;
        submitButton.style.opacity = '1';
        submitButton.style.cursor = 'pointer';
      } else {
        emailWarning.style.display = 'none';
        emailSuccess.style.display = 'none';
        submitButton.disabled = false;
        submitButton.style.opacity = '1';
        submitButton.style.cursor = 'pointer';
      }
    });
  </script>
</body>
</html>