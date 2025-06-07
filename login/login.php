<?php 
session_start();

if (isset($_SESSION['loggedin'])) {
    header('Location: ../perfil.php');
    exit;
}

$erro = isset($_GET['erro']) ? (int)$_GET['erro'] : 0;
$mensagem_erro = '';
switch ($erro) {
    case 1: $mensagem_erro = 'Credenciais incorretas'; break;
    case 2: $mensagem_erro = 'Preencha todos os campos'; break;
    case 3: $mensagem_erro = 'Conta inativa'; break;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Login</title>
  <?php include '../includes/head.html'; ?>
  <style>
    main {
      max-width: 500px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    h2 {
      color: #001f3f;
      text-align: center;
      margin-bottom: 1.5rem;
    }
    
    label {
      display: block;
      margin-bottom: 0.5rem;
      color: #001f3f;
      font-weight: 500;
    }
    
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }
    
    button[type="submit"] {
      width: 100%;
      padding: 0.75rem;
      background-color: #001f3f;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    button[type="submit"]:hover {
      background-color: #003366;
    }
    
    .error-message {
      color: #d9534f;
      margin-bottom: 1rem;
      padding: 0.75rem;
      background-color: #f8d7da;
      border-radius: 4px;
      text-align: center;
    }
    
    .links {
      margin-top: 1.5rem;
      text-align: center;
      font-size: 0.9rem;
    }
    
    .links a {
      color: #001f3f;
      text-decoration: none;
      margin: 0 0.5rem;
    }
    
    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>
  
  <main class="container">
    <div class="login-container">
      <div class="card login-card">
        <div class="card-header login-header text-center py-3">
          <h4>Iniciar Sessão</h4>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($mensagem_erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
          <?php endif; ?>

          <form action="processa_login.php" method="POST">
            <div class="mb-3">
              <label for="login" class="form-label">Username ou Email</label>
              <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="mb-3">
              <label for="senha" class="form-label">Senha</label>
              <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
          </form>

          <div class="mt-3 text-center">
            <a href="registar.php" class="text-decoration-none">Criar nova conta</a>
            <span class="mx-2">•</span>
            <a href="recuperar_senha.php" class="text-decoration-none">Esqueci a senha</a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
</body>
</html>