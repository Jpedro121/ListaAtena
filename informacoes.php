<?php
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Informações</title>
  <?php include 'includes/head.html'; ?>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="flex-grow-1 container py-4">
    <h2>Notícias e Informações</h2>

    <?php
    $sql = "SELECT * FROM informacoes ORDER BY data_publicacao DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0):
      while ($info = $result->fetch_assoc()):
    ?>
      <div class="info-bloco mb-4">
        <h3><?= htmlspecialchars($info['titulo']) ?></h3>
        <p><small><em>Publicado em: <?= $info['data_publicacao'] ?></em></small></p>
        <p><?= nl2br(htmlspecialchars($info['conteudo'])) ?></p>
      </div>
    <?php
      endwhile;
    else:
      echo "<p>Sem informações disponíveis no momento.</p>";
    endif;
    ?>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
