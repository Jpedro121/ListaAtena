<?php
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Eventos</title>
  <?php include 'includes/head.html'; ?>
</head>
<body>
  <?php include 'includes/header.php'; ?>
  <main>
    <h2>Eventos</h2>
    <?php
    $sql = "SELECT * FROM eventos ORDER BY data ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0):
      while ($evento = $result->fetch_assoc()):
    ?>
      <div class="evento">
        <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
        <p><strong>Data:</strong> <?= $evento['data'] ?> às <?= $evento['hora'] ?></p>
        <p><?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
      </div>
    <?php
      endwhile;
    else:
      echo "<p>Sem eventos registados de momento.</p>";
    endif;
    ?>
  </main>
  <footer>
    <p>&copy; 2025 Associação de Estudantes</p>
  </footer>
</body>
</html>
