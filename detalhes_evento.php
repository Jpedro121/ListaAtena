<?php
session_start(); // Adicione esta linha no início
require 'includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$evento = $stmt->get_result()->fetch_assoc();

if (!$evento) {
    header('Location: index.php');
    exit;
}

// Verificar se o usuário está inscrito (se logado)
$inscrito = false;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { // Verificação mais segura
    $stmt = $conn->prepare("SELECT 1 FROM inscricoes_eventos WHERE id_utilizador = ? AND id_evento = ?");
    $stmt->bind_param("ii", $_SESSION['id'], $id);
    $stmt->execute();
    $inscrito = $stmt->get_result()->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($evento['titulo']) ?> | Nome do Site</title>
    <?php include 'includes/head.html'; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container py-4">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= $evento['tipo'] == 'palestra' ? 'palestras.php' : 'eventos.php' ?>">
                            <?= ucfirst($evento['tipo']) ?>s
                        </a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($evento['titulo']) ?></li>
                    </ol>
                </nav>

                <article>
                    <header class="mb-4">
                        <h1><?= htmlspecialchars($evento['titulo']) ?></h1>
                        <div class="d-flex flex-wrap gap-2 my-3">
                            <span class="badge bg-primary"><?= ucfirst($evento['tipo']) ?></span>
                            <span class="badge bg-secondary">
                                <i class="bi bi-calendar-event me-1"></i>
                                <?= date('d/m/Y', strtotime($evento['data'])) ?>
                                <?= $evento['hora'] ? ' - ' . date('H:i', strtotime($evento['hora'])) : '' ?>
                            </span>
                            <?php if ($evento['local']): ?>
                            <span class="badge bg-secondary">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?= htmlspecialchars($evento['local']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if ($evento['imagem']): ?>
                    <figure class="figure mb-4">
                        <img src="uploads/eventos/<?= htmlspecialchars($evento['imagem']) ?>" 
                             class="figure-img img-fluid rounded" 
                             alt="<?= htmlspecialchars($evento['titulo']) ?>">
                    </figure>
                    <?php endif; ?>

                    <div class="mb-4">
                        <?= nl2br(htmlspecialchars($evento['descricao'])) ?>
                    </div>

                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <form method="post" action="processar_inscricao.php">
                            <input type="hidden" name="id_evento" value="<?= $evento['id'] ?>">
                            <?php if ($inscrito): ?>
                                <button type="submit" name="cancelar" class="btn btn-outline-danger">
                                    Cancelar Inscrição
                                </button>
                                <small class="text-muted ms-2">Você está inscrito neste evento</small>
                            <?php else: ?>
                                <button type="submit" name="inscrever" class="btn btn-primary">
                                    Inscrever-se
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <a href="login/login.php" class="alert-link">Faça login</a> para se inscrever neste evento.
                        </div>
                    <?php endif; ?>
                </article>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>