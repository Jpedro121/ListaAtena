<?php
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Palestras</title>
  <?php include 'includes/head.html'; ?>
</head>
<body>
  <?php include 'includes/header.php'; ?>
  <main>
    <h2>Palestras</h2>
    <?php
    $sql = "SELECT * FROM palestras ORDER BY data ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0):
      while ($palestra = $result->fetch_assoc()):
    ?>
      <div class="palestra">
        <h3><?= htmlspecialchars($palestra['tema']) ?></h3>
        <p><strong>Data:</strong> <?= $palestra['data'] ?></p>
        <p><strong>Orador:</strong> <?= htmlspecialchars($palestra['orador']) ?></p>
        <p><?= nl2br(htmlspecialchars($palestra['descricao'])) ?></p>
      </div>
    <?php
      endwhile;
    else:
      echo "<p>Sem palestras disponíveis de momento.</p>";
    endif;
    ?>
  </main>
  <footer>
    <p>&copy; 2025 Associação de Estudantes</p>
  </footer>
</body>
</html>
