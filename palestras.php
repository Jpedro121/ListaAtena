<?php
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Palestras</title>
  <?php include 'includes/head.html'; ?>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="flex-grow-1 container py-4">
    <h2>Palestras</h2>
    <?php
    $sql = "SELECT * FROM palestras ORDER BY data ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0):
      while ($palestra = $result->fetch_assoc()):
    ?>
      <div class="palestra mb-4">
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

  <?php include 'includes/footer.php'; ?>
</body>
</html>
