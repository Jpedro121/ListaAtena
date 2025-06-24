<?php
session_start();

require 'includes/db.php';

// Configurações de paginação
$itensPorPagina = 6;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Busca o total de palestras futuras
$totalQuery = $conn->prepare("SELECT COUNT(*) as total FROM palestras WHERE data >= CURDATE()");
$totalQuery->execute();
$totalItens = $totalQuery->get_result()->fetch_assoc()['total'];
$totalPaginas = ceil($totalItens / $itensPorPagina);

// Busca palestras futuras com paginação (usando prepared statement)
$sql = "SELECT id, tema, data, hora, orador, descricao, local, imagem 
        FROM palestras 
        WHERE data >= CURDATE()
        ORDER BY data ASC, hora ASC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $itensPorPagina, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Busca palestras passadas (limitado a 5)
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
    :root {
      --primary-color: #001f3f;
      --secondary-color: #6c757d;
      --highlight-color: #ffc107;
    }
    
    .palestra-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-left: 4px solid var(--primary-color);
      margin-bottom: 1.5rem;
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    
    .palestra-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .palestra-img-container {
      height: 200px;
      overflow: hidden;
      position: relative;
    }
    
    .palestra-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .palestra-card:hover .palestra-img {
      transform: scale(1.05);
    }
    
    .badge-data {
      background-color: var(--primary-color);
      font-size: 0.9rem;
    }
    
    .empty-state {
      padding: 3rem;
      text-align: center;
      background-color: #f8f9fa;
      border-radius: 0.5rem;
      border: 1px dashed #dee2e6;
    }
    
    .past-lecture {
      border-left: 3px solid var(--secondary-color);
      padding-left: 1rem;
      margin-bottom: 0.5rem;
      transition: border-color 0.3s ease;
    }
    
    .past-lecture:hover {
      border-left-color: var(--primary-color);
    }
    
    .today-badge {
      background-color: var(--highlight-color);
      color: #000;
    }
    
    .page-item.active .page-link {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .page-link {
      color: var(--primary-color);
    }
    
    @media (max-width: 767.98px) {
      .palestra-img-container {
        height: 150px;
      }
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="flex-grow-1 container py-4">
    <div class="row mb-4">
      <div class="col">
        <h1 class="mb-3">Palestras e Eventos</h1>
        <p class="lead text-muted">Confira nossa programação de palestras e eventos acadêmicos</p>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="h4 mb-0">Próximas Palestras</h2>
          <?php if ($totalItens > 0): ?>
            <span class="badge bg-primary"><?= $totalItens ?> eventos agendados</span>
          <?php endif; ?>
        </div>
        
        <?php if ($result && $result->num_rows > 0): ?>
          <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php while ($palestra = $result->fetch_assoc()): 
              $isToday = date('Y-m-d') == $palestra['data'];
              ?>
              <div class="col">
                <div class="card palestra-card h-100">
                  <?php if (!empty($palestra['imagem'])): ?>
                    <div class="palestra-img-container">
                      <img src="uploads/palestras/<?= htmlspecialchars($palestra['imagem']) ?>" 
                           class="palestra-img" 
                           alt="<?= htmlspecialchars($palestra['tema']) ?>"
                           loading="lazy">
                    </div>
                  <?php endif; ?>
                  
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <span class="badge badge-data mb-2">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?= date('d/m/Y', strtotime($palestra['data'])) ?>
                        <?php if (!empty($palestra['hora'])): ?>
                          • <?= date('H:i', strtotime($palestra['hora'])) ?>
                        <?php endif; ?>
                      </span>
                      <?php if ($isToday): ?>
                        <span class="badge today-badge">
                          <i class="bi bi-exclamation-triangle me-1"></i> Hoje!
                        </span>
                      <?php endif; ?>
                    </div>
                    
                    <h3 class="h5 card-title"><?= htmlspecialchars($palestra['tema']) ?></h3>
                    <p class="card-text text-muted">
                      <i class="bi bi-person me-1"></i>
                      <?= htmlspecialchars($palestra['orador']) ?>
                    </p>
                    
                    <?php if (!empty($palestra['local'])): ?>
                      <p class="card-text">
                        <i class="bi bi-geo-alt me-1"></i>
                        <small><?= htmlspecialchars($palestra['local']) ?></small>
                      </p>
                    <?php endif; ?>
                    
                    <p class="card-text text-truncate-3"><?= nl2br(htmlspecialchars($palestra['descricao'])) ?></p>
                    
                    <a href="detalhes_palestra.php?id=<?= $palestra['id'] ?>" class="btn btn-sm btn-outline-primary mt-auto">
                      Ver detalhes <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

          <!-- Paginação melhorada -->
          <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Navegação de páginas" class="mt-5">
              <ul class="pagination justify-content-center">
                <?php if ($paginaAtual > 1): ?>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=1" aria-label="Primeira">
                      <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $paginaAtual - 1 ?>" aria-label="Anterior">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                <?php endif; ?>

                <?php 
                // Mostrar apenas algumas páginas próximas à atual
                $start = max(1, $paginaAtual - 2);
                $end = min($totalPaginas, $paginaAtual + 2);
                
                if ($start > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                
                for ($i = $start; $i <= $end; $i++): ?>
                  <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                  </li>
                <?php endfor;
                
                if ($end < $totalPaginas) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                ?>

                <?php if ($paginaAtual < $totalPaginas): ?>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $paginaAtual + 1 ?>" aria-label="Próximo">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $totalPaginas ?>" aria-label="Última">
                      <span aria-hidden="true">&raquo;&raquo;</span>
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
            <a href="sugerir_palestra.php" class="btn btn-outline-primary mt-2">
              <i class="bi bi-lightbulb me-1"></i> Sugerir uma palestra
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar com palestras passadas e filtros -->
      <div class="col-lg-4 mt-5 mt-lg-0">
        <div class="sticky-top" style="top: 20px;">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h3 class="h5 card-title mb-3">Filtrar Palestras</h3>
              <form method="get" action="busca_palestras.php">
                <div class="mb-3">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data" name="data">
                </div>
                <div class="mb-3">
                  <label for="orador" class="form-label">Orador</label>
                  <input type="text" class="form-control" id="orador" name="orador" placeholder="Nome do palestrante">
                </div>
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
              </form>
            </div>
          </div>
          
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h3 class="h5 card-title mb-3">Palestras Anteriores</h3>
              
              <?php if ($passadas && $passadas->num_rows > 0): ?>
                <div class="list-group list-group-flush">
                  <?php while ($passada = $passadas->fetch_assoc()): ?>
                    <a href="detalhes_palestra.php?id=<?= $passada['id'] ?>" class="list-group-item list-group-item-action past-lecture">
                      <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 h6"><?= htmlspecialchars($passada['tema']) ?></h5>
                        <small><?= date('d/m/Y', strtotime($passada['data'])) ?></small>
                      </div>
                      <p class="mb-1 text-muted">
                        <small><?= htmlspecialchars($passada['orador']) ?></small>
                      </p>
                    </a>
                  <?php endwhile; ?>
                </div>
                <a href="arquivo_palestras.php" class="btn btn-outline-secondary w-100 mt-3">
                  <i class="bi bi-archive me-1"></i> Ver arquivo completo
                </a>
              <?php else: ?>
                <p class="text-muted">Nenhuma palestra passada registrada.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
  
  <script>
    // Efeito de hover melhorado para cards
    document.querySelectorAll('.palestra-card').forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
      });
      card.addEventListener('mouseleave', () => {
        card.style.boxShadow = '';
      });
    });
    
    // Atualizar campo de data para a data atual
    document.getElementById('data').valueAsDate = new Date();
  </script>
</body>
</html>