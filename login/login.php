<?php
session_start();

// Se já está logado, vai para o home ou perfil
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: ../home.php');
    exit;
}

// Tratamento de erros de forma segura
$erro = isset($_GET['erro']) ? (int)$_GET['erro'] : 0;
$mensagens_erro = [
    1 => 'Credenciais incorretas. Por favor, tente novamente.',
    2 => 'Por favor, preencha todos os campos obrigatórios.',
    3 => 'Sua conta está inativa. Entre em contato com o suporte.',
    4 => 'Sessão expirada. Faça login novamente.',
    5 => 'Acesso não autorizado. Faça login para continuar.'
];

$mensagem_erro = $mensagens_erro[$erro] ?? '';

// Proteção contra CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página de login do sistema">
    <title>Login | Sistema</title>
    <?php include '../includes/head.html'; ?>
    <style>
        :root {
            --primary-color: #001f3f;
            --primary-hover: #003366;
            --error-color: #d9534f;
            --error-bg: #f8d7da;
            --success-color: #28a745;
            --text-color: #212529;
            --border-color: #ced4da;
        }

        body {
            background-color: #f8f9fa;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        main {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 31, 63, 0.25);
            outline: none;
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .alert {
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            border-radius: 4px;
            text-align: center;
        }

        .alert-danger {
            color: var(--error-color);
            background-color: var(--error-bg);
            border: 1px solid rgba(217, 83, 79, 0.3);
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .links a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .separator {
            display: inline-block;
            margin: 0 0.5rem;
            color: var(--border-color);
        }

        /* Acessibilidade */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Responsividade */
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
        <h2 id="login-heading">Iniciar Sessão</h2>
        
        <?php if (!empty($mensagem_erro)): ?>
            <div class="alert alert-danger" role="alert" aria-live="assertive">
                <?= htmlspecialchars($mensagem_erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="processa_login.php" method="POST" autocomplete="on" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="mb-3">
                <label for="login" class="form-label">Email ou Nome de Usuário</label>
                <input type="text" class="form-control" id="login" name="login" required
                       aria-required="true" aria-describedby="login-help">
                <small id="login-help" class="sr-only">Insira seu email ou nome de usuário</small>
            </div>
            
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required
                       aria-required="true" minlength="8">
                <small class="d-block mt-1 text-end">
                    <a href="recuperar_senha.php" class="text-decoration-none">Esqueceu a senha?</a>
                </small>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="lembrar" name="lembrar">
                <label class="form-check-label" for="lembrar">Manter-me conectado</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <div class="links">
            <span>Não tem uma conta?</span>
            <a href="registar.php">Cadastre-se</a>
            <span class="separator">|</span>
            <a href="../">Voltar ao site</a>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Foco automático no primeiro campo
        document.addEventListener('DOMContentLoaded', function() {
            const loginInput = document.getElementById('login');
            if (loginInput) {
                loginInput.focus();
            }
            
            // Validação básica do formulário
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const login = document.getElementById('login').value.trim();
                    const senha = document.getElementById('senha').value.trim();
                    
                    if (!login || !senha) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios.');
                    }
                });
            }
        });
    </script>
</body>
</html>