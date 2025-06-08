<?php
session_start();

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['loggedin']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

// Inclui o arquivo de conexão
require_once __DIR__ . '/../includes/db.php';

// Verificar se o ID do evento foi passado e é válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: eventos.php");
    exit();
}

$evento_id = intval($_GET['id']);

// Buscar informações do evento
$sql = "SELECT * FROM eventos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$result = $stmt->get_result();
$evento = $result->fetch_assoc();

if (!$evento) {
    header("Location: eventos.php");
    exit();
}

// Processar formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $data = $_POST['data'] ?? '';
    $local = $_POST['local'] ?? '';
    $limite_inscricoes = isset($_POST['limite_inscricoes']) ? intval($_POST['limite_inscricoes']) : 0;

    $sql = "UPDATE eventos SET titulo = ?, descricao = ?, data = ?, local = ?, limite_inscricoes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $nome, $descricao, $data, $local, $limite_inscricoes, $evento_id);
    $stmt->execute();

    header("Location: eventos.php?success=1");
    exit();
}

// Buscar lista de inscritos no evento
$sql = "SELECT u.id, u.nome, u.email, ie.data_inscricao 
        FROM inscricoes_eventos ie
        JOIN utilizadores u ON ie.id_utilizador = u.id
        WHERE ie.id_evento = ?
        ORDER BY ie.data_inscricao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$inscritos = $stmt->get_result();
$total_inscritos = $inscritos->num_rows;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Evento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { background: #001f3f; min-height: 100vh; }
        .sidebar .nav-link { color: #fff; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #495057; color: #fff; }
        .form-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 100%; max-width: 900px; }
        .inscritos-list { max-height: 300px; overflow-y: auto; }
        .progress { height: 25px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="eventos_admin.php">
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
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 form-container">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title mb-4 text-center">Editar Evento</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nome do Evento:</label>
                                    <input type="text" name="nome" class="form-control" 
                                           value="<?= htmlspecialchars($evento['nome'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descrição:</label>
                                    <textarea name="descricao" class="form-control" required><?= 
                                        htmlspecialchars($evento['descricao'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Data e Hora:</label>
                                    <input type="datetime-local" name="data" class="form-control" 
                                           value="<?= isset($evento['data']) ? date('Y-m-d\TH:i', strtotime($evento['data'])) : '' ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Local:</label>
                                    <input type="text" name="local" class="form-control" 
                                           value="<?= htmlspecialchars($evento['local'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Limite de Inscrições (0 para ilimitado):</label>
                                    <input type="number" name="limite_inscricoes" class="form-control" min="0" 
                                           value="<?= $evento['limite_inscricoes'] ?? 0 ?>">
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="eventos_admin.php" class="btn btn-secondary">Voltar</a>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h4>Inscrições no Evento</h4>
                            <?php if (($evento['limite_inscricoes'] ?? 0) > 0): ?>
                            <div class="mb-3">
                                <label>Progresso de Inscrições</label>
                                <div class="progress">
                                    <?php
                                    $limite = $evento['limite_inscricoes'] ?? 1;
                                    $percent = ($total_inscritos / $limite) * 100;
                                    $progress_class = ($percent >= 100) ? 'bg-danger' : (($percent >= 80) ? 'bg-warning' : 'bg-success');
                                    ?>
                                    <div class="progress-bar <?= $progress_class ?>" role="progressbar" style="width: <?= min($percent, 100) ?>%" 
                                         aria-valuenow="<?= $total_inscritos ?>" aria-valuemin="0" aria-valuemax="<?= $limite ?>">
                                        <?= $total_inscritos ?> / <?= $limite ?>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                                <p>Total de inscritos: <?= $total_inscritos ?> (sem limite)</p>
                            <?php endif; ?>
                            <div class="card inscritos-list mb-3">
                                <div class="card-body">
                                    <?php if ($inscritos->num_rows > 0): ?>
                                        <ul class="list-group">
                                            <?php while ($inscrito = $inscritos->fetch_assoc()): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= htmlspecialchars($inscrito['nome'] ?? '') ?></strong><br>
                                                        <small><?= htmlspecialchars($inscrito['email'] ?? '') ?></small><br>
                                                        <small>Inscrito em: <?= 
                                                            isset($inscrito['data_inscricao']) ? date('d/m/Y H:i', strtotime($inscrito['data_inscricao'])) : '' 
                                                        ?></small>
                                                    </div>
                                                    <a href="remover_inscricao.php?evento_id=<?= $evento_id ?>&usuario_id=<?= $inscrito['id'] ?? '' ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Tem certeza que deseja remover esta inscrição?')">
                                                        Remover
                                                    </a>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>Nenhum inscrito neste evento ainda.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="exportar_inscritos.php?id=<?= $evento_id ?>" class="btn btn-success">Exportar Lista de Inscritos</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>