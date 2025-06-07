<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/login.php?erro=5');
    exit;
}

require 'includes/db.php';

// Configurações de paginação
$itensPorPagina = 6;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Busca o total de palestras
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM palestras WHERE data >= CURDATE()");
$totalItens = $totalQuery->fetch_assoc()['total'];
$totalPaginas = ceil($totalItens / $itensPorPagina);

// Busca palestras futuras com paginação
$sql = "SELECT id, tema, data, hora, orador, descricao, local, imagem 
        FROM palestras 
        WHERE data >= CURDATE()
        ORDER BY data ASC, hora ASC
        LIMIT $itensPorPagina OFFSET $offset";
$result = $conn->query($sql);

// Busca palestras passadas (separadamente)
$sqlPassadas = "SELECT id, tema, data, hora, orador 
                FROM palestras 
                WHERE data < CURDATE()
                ORDER BY data DESC
                LIMIT 5";
$passadas = $conn->query($sqlPassadas);

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Palestras e Eventos - Lista Atena</title>
  <?php include 'includes/head.html'; ?>
  <style>
    .palestra-card {
      transition: all 0.3s ease;
      border-left: 4px solid #001f3f;
      margin-bottom: 1.5rem;
      height: 100%;
    }
    .palestra-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .palestra-img {
      height: 200px;
      object-fit: cover;
      width: 100%;
    }
    .badge-data {
      background-color: #001f3f;
      font-size: 0.9rem;
    }
    .empty-state {
      padding: 3rem;
      text-align: center;
      background-color: #f8f9fa;
      border-radius: 0.5rem;
    }
    .past-lecture {
      border-left: 3px solid #6c757d;
      padding-left: 1rem;
      margin-bottom: 0.5rem;
    }
    .past-lecture:hover {
      border-left-color: #001f3f;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="flex-grow-1 container py-4">
    <div class="row mb-4">
      <div class="col">
        <h1 class="mb-3">Palestras e Eventos</h1>
        <p class="lead">Confira nossa programação de palestras e eventos acadêmicos</p>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <h2 class="h4 mb-4">Próximas Palestras</h2>
        
        <?php if ($result->num_rows > 0): ?>
          <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php while ($palestra = $result->fetch_assoc()): ?>
              <div class="col">
                <div class="card palestra-card h-100">
                  <?php if (!empty($palestra['imagem'])): ?>
                    <img src="uploads/palestras/<?= htmlspecialchars($palestra['imagem']) ?>" 
                         class="palestra-img card-img-top" 
                         alt="<?= htmlspecialchars($palestra['tema']) ?>"
                         loading="lazy">
                  <?php endif; ?>
                  
                  <div class="card-body">
                    <span class="badge badge-data mb-2">
                      <i class="bi bi-calendar-event me-1"></i>
                      <?= date('d/m/Y', strtotime($palestra['data'])) ?>
                      <?php if (!empty($palestra['hora'])): ?>
                        • <?= date('H:i', strtotime($palestra['hora'])) ?>
                      <?php endif; ?>
                    </span>
                    
                    <h3 class="h5 card-title"><?= htmlspecialchars($palestra['tema']) ?></h3>
                    <p class="card-text text-muted">
                      <i class="bi bi-person me-1"></i>
                      <?= htmlspecialchars($palestra['orador']) ?>
                    </p>
                    
                    <?php if (!empty($palestra['local'])): ?>
                      <p class="card-text">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?= htmlspecialchars($palestra['local']) ?>
                      </p>
                    <?php endif; ?>
                    
                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($palestra['descricao'], 0, 150))) ?>...</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                      <a href="detalhes_palestra.php?id=<?= $palestra['id'] ?>" class="btn btn-sm btn-outline-primary">
                        Detalhes
                      </a>
                      <?php if (!empty($palestra['hora']) && strtotime($palestra['data']) == strtotime(date('Y-m-d'))): ?>
                        <span class="badge bg-warning text-dark">
                          <i class="bi bi-exclamation-triangle me-1"></i> Hoje!
                        </span>
                      <?php endif; ?>
                    </div>
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
            <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
            <h3 class="h5">Nenhuma palestra agendada</h3>
            <p class="text-muted">Novas palestras serão anunciadas em breve. Fique atento!</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar com palestras passadas -->
      <div class="col-lg-4 mt-5 mt-lg-0">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h5 card-title mb-4">Palestras Anteriores</h3>
            
            <?php if ($passadas->num_rows > 0): ?>
              <div class="list-group list-group-flush">
                <?php while ($passada = $passadas->fetch_assoc()): ?>
                  <a href="detalhes_palestra.php?id=<?= $passada['id'] ?>" class="list-group-item list-group-item-action past-lecture">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1"><?= htmlspecialchars($passada['tema']) ?></h5>
                      <small><?= date('d/m/Y', strtotime($passada['data'])) ?></small>
                    </div>
                    <p class="mb-1 text-muted">
                      <small><?= htmlspecialchars($passada['orador']) ?></small>
                    </p>
                  </a>
                <?php endwhile; ?>
              </div>
              <a href="arquivo_palestras.php" class="btn btn-sm btn-outline-secondary mt-3">Ver todas</a>
            <?php else: ?>
              <p class="text-muted">Nenhuma palestra passada registrada.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>