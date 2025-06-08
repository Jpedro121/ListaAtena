<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../login/login.php');
    exit;
}

require 'includes/db.php';

// Processa o formulário se for enviado
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação dos campos
    $tema = trim($_POST['tema'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $orador = trim($_POST['orador'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_sugerida = trim($_POST['data_sugerida'] ?? '');
    $justificativa = trim($_POST['justificativa'] ?? '');

    // Validações
    if (empty($tema)) {
        $errors['tema'] = 'Por favor, informe o tema da palestra';
    } elseif (strlen($tema) > 100) {
        $errors['tema'] = 'O tema deve ter no máximo 100 caracteres';
    }

    if (empty($descricao)) {
        $errors['descricao'] = 'Por favor, forneça uma descrição';
    }

    if (empty($orador)) {
        $errors['orador'] = 'Por favor, informe o nome do palestrante';
    }

    if (!empty($data_sugerida) && !strtotime($data_sugerida)) {
        $errors['data_sugerida'] = 'Data inválida';
    }

    // Se não houver erros, insere no banco
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO sugestoes_palestras 
                               (tema, descricao, orador, email, data_sugerida, justificativa, usuario_id, data_criacao) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssssi", 
                         $tema, 
                         $descricao, 
                         $orador, 
                         $email, 
                         $data_sugerida, 
                         $justificativa, 
                         $_SESSION['userid']);
        
        if ($stmt->execute()) {
            $success = true;
            
            // Envia email para o admin (opcional)
            $to = 'admin@escola.com';
            $subject = 'Nova sugestão de palestra: ' . $tema;
            $message = "Uma nova palestra foi sugerida:\n\n";
            $message .= "Tema: $tema\n";
            $message .= "Orador: $orador\n";
            $message .= "Data Sugerida: " . ($data_sugerida ?: 'Não especificada') . "\n";
            $message .= "Justificativa: $justificativa\n\n";
            $message .= "Sugerido por: " . $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
            
            mail($to, $subject, $message);
        } else {
            $errors['db'] = 'Erro ao salvar sugestão. Por favor, tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerir Palestra - Lista Atena</title>
    <?php include 'includes/head.html'; ?>
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-header {
            border-bottom: 2px solid #001f3f;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .form-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        .form-card-body {
            padding: 2rem;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
        }
        .success-message {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow-1 py-4">
        <div class="container form-container">
            <div class="form-header text-center">
                <h1 class="h3 mb-2">Sugerir Nova Palestra</h1>
                <p class="text-muted">Contribua com sugestões para nosso programa de palestras</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Sugestão enviada com sucesso!</h4>
                    <p>Sua sugestão de palestra foi registrada e será analisada pela equipe responsável.</p>
                    <hr>
                    <p class="mb-0">Obrigado por contribuir com nosso programa acadêmico.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="palestras.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para Palestras
                    </a>
                </div>
            <?php else: ?>
                <div class="form-card">
                    <div class="form-card-body">
                        <?php if (!empty($errors['db'])): ?>
                            <div class="alert alert-danger mb-4">
                                <?= htmlspecialchars($errors['db']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="tema" class="form-label">Tema da Palestra *</label>
                                    <input type="text" class="form-control <?= isset($errors['tema']) ? 'is-invalid' : '' ?>" 
                                           id="tema" name="tema" value="<?= htmlspecialchars($_POST['tema'] ?? '') ?>" required>
                                    <?php if (isset($errors['tema'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['tema']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="orador" class="form-label">Nome do Palestrante *</label>
                                    <input type="text" class="form-control <?= isset($errors['orador']) ? 'is-invalid' : '' ?>" 
                                           id="orador" name="orador" value="<?= htmlspecialchars($_POST['orador'] ?? '') ?>" required>
                                    <?php if (isset($errors['orador'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['orador']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição da Palestra *</label>
                                <textarea class="form-control <?= isset($errors['descricao']) ? 'is-invalid' : '' ?>" 
                                          id="descricao" name="descricao" rows="4" required><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                                <div class="form-text">Descreva os principais tópicos que serão abordados</div>
                                <?php if (isset($errors['descricao'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['descricao']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="data_sugerida" class="form-label">Data Sugerida (opcional)</label>
                                    <input type="date" class="form-control <?= isset($errors['data_sugerida']) ? 'is-invalid' : '' ?>" 
                                           id="data_sugerida" name="data_sugerida" value="<?= htmlspecialchars($_POST['data_sugerida'] ?? '') ?>">
                                    <?php if (isset($errors['data_sugerida'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['data_sugerida']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email do Palestrante (opcional)</label>
                                    <input type="email" class="form-control" 
                                           id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                    <div class="form-text">Caso tenha informações de contato</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="justificativa" class="form-label">Justificativa *</label>
                                <textarea class="form-control" id="justificativa" name="justificativa" rows="3" required><?= htmlspecialchars($_POST['justificativa'] ?? '') ?></textarea>
                                <div class="form-text">Por que esta palestra seria importante para nossa comunidade acadêmica?</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="palestras.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i> Enviar Sugestão
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Validação do lado do cliente
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Valida tema
                    const tema = document.getElementById('tema');
                    if (tema.value.trim() === '') {
                        tema.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    // Valida orador
                    const orador = document.getElementById('orador');
                    if (orador.value.trim() === '') {
                        orador.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    // Valida descrição
                    const descricao = document.getElementById('descricao');
                    if (descricao.value.trim() === '') {
                        descricao.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    // Valida justificativa
                    const justificativa = document.getElementById('justificativa');
                    if (justificativa.value.trim() === '') {
                        justificativa.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                    }
                });
                
                // Remove a classe de erro quando o usuário começa a digitar
                document.querySelectorAll('.is-invalid').forEach(element => {
                    element.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    });
                });
            }
            
            // Configura a data mínima para o campo de data (hoje)
            const dataField = document.getElementById('data_sugerida');
            if (dataField) {
                const today = new Date().toISOString().split('T')[0];
                dataField.min = today;
            }
        });
    </script>
</body>
</html>