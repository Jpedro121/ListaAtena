<?php
require 'includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: eventos.php');
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM eventos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$evento = $result->fetch_assoc();

if (!$evento) {
    header('Location: eventos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title><?= htmlspecialchars($evento['titulo']) ?></title>
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
        
        <?php if ($evento['imagem']): ?>
          <img src="static/eventos/<?= htmlspecialchars($evento['imagem']) ?>" class="img-fluid rounded mb-4" alt="<?= htmlspecialchars($evento['titulo']) ?>">
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
              <li class="list-group-item">
                <strong>Tipo:</strong> <?= ucfirst($evento['tipo']) ?>
              </li>
              <li class="list-group-item">
                <strong>Data:</strong> <?= date('d/m/Y', strtotime($evento['data'])) ?>
              </li>
              <?php if ($evento['hora']): ?>
              <li class="list-group-item">
                <strong>Hora:</strong> <?= date('H:i', strtotime($evento['hora'])) ?>
              </li>
              <?php endif; ?>
              <?php if ($evento['local']): ?>
              <li class="list-group-item">
                <strong>Local:</strong> <?= htmlspecialchars($evento['local']) ?>
              </li>
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