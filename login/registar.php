<?php
session_start();

// Recupera mensagens de erro e valores preenchidos anteriormente
$erro = $_SESSION['registo_erro'] ?? '';
$valores = $_SESSION['registo_campos'] ?? [];
unset($_SESSION['registo_erro']);
unset($_SESSION['registo_campos']);

// Mensagem de sucesso
$sucesso = $_SESSION['registo_sucesso'] ?? '';
unset($_SESSION['registo_sucesso']);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registar | AE ESJS</title>
  <?php include '../includes/head.html'; ?>
  <style>
    :root {
      --primary-color: #001f3f;
      --error-color: #d9534f;
      --success-color: #5cb85c;
      --warning-color: #f0ad4e;
      --text-color: #333;
      --border-color: #ddd;
    }

    main {
      max-width: 500px;
      margin: 2rem auto;
      padding: 2rem;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    h2 {
      color: var(--primary-color);
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--primary-color);
      font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    input:focus {
      border-color: var(--primary-color);
      outline: none;
      box-shadow: 0 0 0 2px rgba(0, 31, 63, 0.2);
    }

    .feedback {
      font-size: 0.85rem;
      margin-top: 0.3rem;
      display: none;
    }

    .feedback.error {
      color: var(--error-color);
    }

    .feedback.success {
      color: var(--success-color);
    }

    .feedback.warning {
      color: var(--warning-color);
    }

    #submitButton {
      width: 100%;
      padding: 0.75rem;
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
    }

    #submitButton:hover {
      background-color: #003366;
    }

    #submitButton:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
      opacity: 0.7;
    }

    .alert {
      padding: 0.75rem;
      margin-bottom: 1.5rem;
      border-radius: 4px;
      text-align: center;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: var(--error-color);
      border: 1px solid #f5c6cb;
    }

    .alert-success {
      background-color: #d4edda;
      color: var(--success-color);
      border: 1px solid #c3e6cb;
    }

    .password-strength {
      margin-top: 0.3rem;
      height: 4px;
      background-color: #eee;
      border-radius: 2px;
      overflow: hidden;
    }

    .password-strength-bar {
      height: 100%;
      width: 0;
      transition: width 0.3s, background-color 0.3s;
    }

    @media (max-width: 576px) {
      main {
        margin: 1rem;
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>
  
  <main>
    <h2>Criar Nova Conta</h2>
    
    <?php if ($erro): ?>
      <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    
    <?php if ($sucesso): ?>
      <div class="alert alert-success" role="alert"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form action="processa_registo.php" method="POST" id="registerForm">
      <div class="form-group">
        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" required
               value="<?= htmlspecialchars($valores['nome'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="username">Nome de Utilizador:</label>
        <input type="text" id="username" name="username" required
               pattern="[a-zA-Z0-9_]{4,20}" title="4-20 caracteres (apenas letras, números e _)"
               value="<?= htmlspecialchars($valores['username'] ?? '') ?>">
        <div id="usernameFeedback" class="feedback"></div>
      </div>

      <div class="form-group">
        <label for="email">Email Institucional:</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($valores['email'] ?? '') ?>">
        <div id="emailFeedback" class="feedback error">
          Por favor, utilize um email @esjs-mafra.net
        </div>
      </div>

      <div class="form-group">
        <label for="senha">Palavra-Passe (mínimo 8 caracteres):</label>
        <input type="password" id="senha" name="senha" required minlength="8">
        <div class="password-strength">
          <div id="passwordStrengthBar" class="password-strength-bar"></div>
        </div>
      </div>

      <div class="form-group">
        <label for="confirmar_senha">Confirmar Palavra-Passe:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>
        <div id="passwordMatchFeedback" class="feedback error"></div>
      </div>

      <button type="submit" id="submitButton">Registar</button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem;">
      Já tem uma conta? <a href="login.php">Iniciar Sessão</a>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>

  <script>
    // Validação em tempo real
    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('emailFeedback');
    const usernameInput = document.getElementById('username');
    const usernameFeedback = document.getElementById('usernameFeedback');
    const senhaInput = document.getElementById('senha');
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const submitButton = document.getElementById('submitButton');
    const form = document.getElementById('registerForm');

    // Validação de email
    emailInput.addEventListener('input', function() {
      const email = this.value;
      
      if (email.includes('@') && !email.endsWith('@esjs-mafra.net')) {
        emailFeedback.textContent = 'Por favor, utilize um email @esjs-mafra.net';
        emailFeedback.className = 'feedback error';
        emailFeedback.style.display = 'block';
      } else if (email.endsWith('@esjs-mafra.net')) {
        emailFeedback.textContent = 'Email válido!';
        emailFeedback.className = 'feedback success';
        emailFeedback.style.display = 'block';
      } else {
        emailFeedback.style.display = 'none';
      }
      validateForm();
    });

    // Validação de username
    usernameInput.addEventListener('input', function() {
      const username = this.value;
      const regex = /^[a-zA-Z0-9_]{4,20}$/;
      
      if (username.length > 0 && !regex.test(username)) {
        usernameFeedback.textContent = '4-20 caracteres (apenas letras, números e _)';
        usernameFeedback.className = 'feedback error';
        usernameFeedback.style.display = 'block';
      } else {
        usernameFeedback.style.display = 'none';
      }
      validateForm();
    });

    // Força da senha
    senhaInput.addEventListener('input', function() {
      const password = this.value;
      let strength = 0;
      
      // Verifica comprimento
      if (password.length >= 8) strength += 1;
      if (password.length >= 12) strength += 1;
      
      // Verifica caracteres diversos
      if (/[A-Z]/.test(password)) strength += 1;
      if (/[0-9]/.test(password)) strength += 1;
      if (/[^A-Za-z0-9]/.test(password)) strength += 1;
      
      // Atualiza barra de força
      const width = (strength / 5) * 100;
      passwordStrengthBar.style.width = width + '%';
      
      if (strength <= 2) {
        passwordStrengthBar.style.backgroundColor = '#d9534f'; // Vermelho
      } else if (strength <= 4) {
        passwordStrengthBar.style.backgroundColor = '#f0ad4e'; // Amarelo
      } else {
        passwordStrengthBar.style.backgroundColor = '#5cb85c'; // Verde
      }
      
      // Verifica correspondência
      checkPasswordMatch();
    });

    // Confirmação de senha
    confirmarSenhaInput.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
      const password = senhaInput.value;
      const confirmPassword = confirmarSenhaInput.value;
      
      if (confirmPassword.length > 0 && password !== confirmPassword) {
        passwordMatchFeedback.textContent = 'As senhas não coincidem';
        passwordMatchFeedback.className = 'feedback error';
        passwordMatchFeedback.style.display = 'block';
      } else if (confirmPassword.length > 0) {
        passwordMatchFeedback.textContent = 'As senhas coincidem';
        passwordMatchFeedback.className = 'feedback success';
        passwordMatchFeedback.style.display = 'block';
      } else {
        passwordMatchFeedback.style.display = 'none';
      }
      validateForm();
    }

    // Validação geral do formulário
    function validateForm() {
      const emailValid = emailInput.value.endsWith('@esjs-mafra.net');
      const usernameValid = /^[a-zA-Z0-9_]{4,20}$/.test(usernameInput.value);
      const passwordValid = senhaInput.value.length >= 8;
      const passwordMatch = senhaInput.value === confirmarSenhaInput.value;
      
      submitButton.disabled = !(emailValid && usernameValid && passwordValid && passwordMatch);
    }

    // Validação inicial
    validateForm();

    // Prevenir envio se houver erros
    form.addEventListener('submit', function(e) {
      if (submitButton.disabled) {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>