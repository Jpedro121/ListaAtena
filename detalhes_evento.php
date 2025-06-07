<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/login.php?erro=5');
    exit;
}

require 'includes/db.php';
require 'includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: eventos.php');
    exit;
}

$evento = buscarEventoPorId($conn, $id);
if (!$evento) {
    include 'includes/head.html';
    include 'includes/header.php';
    echo '<main class="container mt-4"><div class="alert alert-warning">Evento não encontrado.</div></main>';
    include 'includes/footer.php';
    exit;
}

// Evita path traversal em imagens
$imagemSegura = isset($evento['imagem']) ? basename($evento['imagem']) : '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($evento['titulo']) ?></title>
  <meta name="description" content="<?= htmlspecialchars(substr($evento['descricao'], 0, 150)) ?>">
  <?php include 'includes/head.html'; ?>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container mt-4">
    <div class="row">
      <div class="col-md-8">
        <h2><?= htmlspecialchars($evento['titulo']) ?></h2>
        <p class="text-muted">
          <i class="bi bi-calendar-event"></i> <?= date('d/m/Y', strtotime($evento['data'])) ?>
          <?php if ($evento['hora']): ?>
            • <i class="bi bi-clock"></i> <?= date('H:i', strtotime($evento['hora'])) ?>
          <?php endif; ?>
          <?php if ($evento['local']): ?>
            • <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($evento['local']) ?>
          <?php endif; ?>
        </p>
        
        <?php if ($imagemSegura): ?>
          <img src="static/eventos/<?= htmlspecialchars($imagemSegura) ?>"
               class="img-fluid rounded mb-4"
               alt="Imagem do evento <?= htmlspecialchars($evento['titulo']) ?>">
        <?php endif; ?>
        
        <div class="mb-4">
          <?= nl2br(htmlspecialchars($evento['descricao'])) ?>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Informações</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><strong>Tipo:</strong> <?= ucfirst(htmlspecialchars($evento['tipo'])) ?></li>
              <li class="list-group-item"><strong>Data:</strong> <?= date('d/m/Y', strtotime($evento['data'])) ?></li>
              <?php if ($evento['hora']): ?>
              <li class="list-group-item"><strong>Hora:</strong> <?= date('H:i', strtotime($evento['hora'])) ?></li>
              <?php endif; ?>
              <?php if ($evento['local']): ?>
              <li class="list-group-item"><strong>Local:</strong> <?= htmlspecialchars($evento['local']) ?></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <a href="eventos.php" class="btn btn-primary mt-4">Voltar aos Eventos</a>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
