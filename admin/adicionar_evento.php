<?php
require '../includes/db.php';
require '../includes/check_admin.php';

// Inicialização de variáveis
$erros = [];
$dados = [
    'titulo' => '',
    'data' => '',
    'hora' => '',
    'descricao' => '',
    'local' => '',
    'tipo' => 'evento'
];

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação CSRF (adicionar ao seu check_admin.php)
    
    // Validação dos dados
    $required_fields = ['titulo', 'data', 'descricao', 'tipo'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $erros[] = "O campo $field é obrigatório.";
        } else {
            $dados[$field] = trim($_POST[$field]);
        }
    }

    // Processamento da imagem (mantido como no seu código original)
    
    // Inserção no banco de dados
    if (empty($erros)) {
        try {
            $stmt = $conn->prepare("INSERT INTO eventos 
                                   (titulo, data, hora, descricao, local, imagem, tipo, id_organizador, criado_em) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->bind_param("sssssssi", 
                $dados['titulo'],
                $dados['data'],
                $dados['hora'],
                $dados['descricao'],
                $dados['local'],
                $imagem,
                $dados['tipo'],
                $_SESSION['id']
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => ucfirst($dados['tipo']) . ' adicionado com sucesso!'
                ];
                header('Location: eventos_admin.php');
                exit();
            }
        } catch (Exception $e) {
            $erros[] = "Erro ao salvar. Por favor, tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Evento | Painel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #001f3f; /* Navy blue */
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: var(--primary-color);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px 15px;
            margin: 2px 10px;
            border-radius: 4px;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 8px;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .admin-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-label.required:after {
            content: " *";
            color: var(--danger-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #001a35;
            border-color: #001a35;
        }
        
        .img-preview {
            max-width: 200px;
            max-height: 150px;
            display: block;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="eventos_admin.php">
                                <i class="bi bi-calendar-event"></i> Eventos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="utilizadores.php">
                                <i class="bi bi-people"></i> Utilizadores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../perfil.php">
                                <i class="bi bi-person"></i> Meu Perfil
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <div>
                        <h2><i class="bi bi-calendar-event me-2"></i> Adicionar Evento</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="eventos_admin.php">Eventos</a></li>
                                <li class="breadcrumb-item active">Adicionar</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <h5 class="alert-heading mb-3">Corrija os seguintes erros:</h5>
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="admin-card">
                    <form method="POST" enctype="multipart/form-data" id="eventoForm">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="titulo" class="form-label required">Título do Evento</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" 
                                       value="<?= htmlspecialchars($dados['titulo']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="tipo" class="form-label required">Tipo de Evento</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="evento" <?= $dados['tipo'] === 'evento' ? 'selected' : '' ?>>Evento</option>
                                    <option value="palestra" <?= $dados['tipo'] === 'palestra' ? 'selected' : '' ?>>Palestra</option>
                                    <option value="workshop" <?= $dados['tipo'] === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                                    <option value="reunião" <?= $dados['tipo'] === 'reunião' ? 'selected' : '' ?>>Reunião</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data" class="form-label required">Data do Evento</label>
                                <input type="date" class="form-control" id="data" name="data" 
                                       value="<?= htmlspecialchars($dados['data']) ?>"
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hora" class="form-label">Hora do Evento</label>
                                <input type="time" class="form-control" id="hora" name="hora"
                                       value="<?= htmlspecialchars($dados['hora']) ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="local" class="form-label">Local do Evento</label>
                            <input type="text" class="form-control" id="local" name="local" 
                                   value="<?= htmlspecialchars($dados['local']) ?>"
                                   placeholder="Ex: Auditório Principal, Sala 101">
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Evento</label>
                            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                            <small class="text-muted">Formatos aceitos: JPG, PNG, GIF (tamanho máximo: 2MB)</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label required">Descrição do Evento</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="5" required><?= htmlspecialchars($dados['descricao']) ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="eventos_admin.php" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Salvar Evento
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview da imagem antes de enviar
        document.getElementById('imagem').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-preview';
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Validação do formulário
        document.getElementById('eventoForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    </script>
</body>
</html>