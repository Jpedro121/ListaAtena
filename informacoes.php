<?php
session_start();

require 'includes/db.php';

// Configurações de paginação
$itensPorPagina = 5;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Busca o total de informações
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM informacoes");
$totalItens = $totalQuery->fetch_assoc()['total'];
$totalPaginas = ceil($totalItens / $itensPorPagina);

// Busca as informações com paginação
$sql = "SELECT id, titulo, data_publicacao, conteudo, imagem 
        FROM informacoes 
        ORDER BY data_publicacao DESC 
        LIMIT $itensPorPagina OFFSET $offset";
$result = $conn->query($sql);

// Fecha a conexão no final
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notícias e Informações - Lista Atena</title>
  <?php include 'includes/head.html'; ?>
  <style>
    .info-card {
      transition: all 0.3s ease;
      border-left: 4px solid #001f3f;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .info-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .info-date {
      color: #6c757d;
      font-size: 0.9rem;
    }
    .pagination .page-item.active .page-link {
      background-color: #001f3f;
      border-color: #001f3f;
    }
    .pagination .page-link {
      color: #001f3f;
    }
    .info-img {
      max-height: 300px;
      object-fit: cover;
      width: 100%;
    }
    .empty-state {
      padding: 3rem;
      text-align: center;
      background-color: #f8f9fa;
      border-radius: 0.5rem;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="flex-grow-1 container py-4">
    <div class="row mb-4">
      <div class="col">
        <h1 class="mb-3">Notícias e Informações</h1>
        <p class="lead">Fique por dentro das últimas novidades e comunicados oficiais</p>
      </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="row">
        <?php while ($info = $result->fetch_assoc()): ?>
          <div class="col-lg-8 mx-auto">
            <div class="card info-card mb-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h2 class="h4 card-title mb-0"><?= htmlspecialchars($info['titulo']) ?></h2>
                  <span class="info-date">
                    <i class="bi bi-calendar me-1"></i>
                    <?= date('d/m/Y H:i', strtotime($info['data_publicacao'])) ?>
                  </span>
                </div>
                
                <?php if (!empty($info['imagem'])): ?>
                  <img src="uploads/informacoes/<?= htmlspecialchars($info['imagem']) ?>" 
                       alt="<?= htmlspecialchars($info['titulo']) ?>" 
                       class="info-img img-fluid rounded mb-3"
                       loading="lazy">
                <?php endif; ?>
                
                <div class="card-text">
                  <?= nl2br(htmlspecialchars($info['conteudo'])) ?>
                </div>
                
                <a href="detalhes_info.php?id=<?= $info['id'] ?>" class="btn btn-outline-primary mt-3">
                  Ler mais <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <!-- Paginação -->
      <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Navegação de páginas" class="mt-4">
          <ul class="pagination justify-content-center">
            <?php if ($paginaAtual > 1): ?>
              <li class="page-item">
                <a class="page-link" href="?pagina=<?= $paginaAtual - 1 ?>" aria-label="Anterior">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
              <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <?php if ($paginaAtual < $totalPaginas): ?>
              <li class="page-item">
                <a class="page-link" href="?pagina=<?= $paginaAtual + 1 ?>" aria-label="Próximo">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-info-circle-fill display-4 text-muted mb-3"></i>
        <h2 class="h4">Nenhuma informação disponível</h2>
        <p class="text-muted">Ainda não há notícias ou informações publicadas. Volte mais tarde!</p>
      </div>
    <?php endif; ?>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>