<?php
require '../includes/check_admin.php';
require '../includes/db.php';

// Verifica se o usuário tem permissão de admin
if ($_SESSION['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Estatísticas para o dashboard
try {
    $sql_users = "SELECT COUNT(*) as total FROM utilizadores";
    $result_users = $conn->query($sql_users);
    $total_users = $result_users ? $result_users->fetch_assoc()['total'] : 0;

    $sql_events = "SELECT COUNT(*) as total FROM eventos";
    $result_events = $conn->query($sql_events);
    $total_events = $result_events ? $result_events->fetch_assoc()['total'] : 0;

    $sql_active_events = "SELECT COUNT(*) as total FROM eventos WHERE data >= CURDATE()";
    $result_active_events = $conn->query($sql_active_events);
    $active_events = $result_active_events ? $result_active_events->fetch_assoc()['total'] : 0;
} catch (Exception $e) {
    // Log do erro
    error_log("Erro ao carregar estatísticas: " . $e->getMessage());
    $total_users = $total_events = $active_events = 0;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Painel de Administração</title>
  <?php include '../includes/head.html'; ?>
  <style>
    .stat-card {
      transition: transform 0.3s;
      border-radius: 10px;
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }
    .admin-sidebar {
      min-height: calc(100vh - 56px);
      background-color: #001f3f;
    }
    .admin-sidebar .nav-link {
      color: rgba(255, 255, 255, 0.8);
    }
    .admin-sidebar .nav-link:hover, 
    .admin-sidebar .nav-link.active {
      color: white;
      background-color: rgba(255, 215, 0, 0.2);
    }
    
    /* Mobile improvements */
    @media (max-width: 768px) {
      .stat-card {
        margin-bottom: 15px;
      }
      .admin-sidebar {
        min-height: auto;
      }
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-none d-md-block admin-sidebar py-3">
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="index.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="eventos_admin.php">
                <i class="bi bi-calendar-event me-2"></i> Eventos
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="utilizadores.php">
                <i class="bi bi-people me-2"></i> Utilizadores
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../perfil.php">
                <i class="bi bi-person me-2"></i> Meu Perfil
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main content -->
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Painel de Administração</h1>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card stat-card bg-primary text-white mb-4">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">UTILIZADORES</h6>
                    <h2 class="mb-0"><?= htmlspecialchars($total_users) ?></h2>
                  </div>
                  <i class="bi bi-people" style="font-size: 2rem; opacity: 0.3;"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card stat-card bg-success text-white mb-4">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">EVENTOS</h6>
                    <h2 class="mb-0"><?= htmlspecialchars($total_events) ?></h2>
                  </div>
                  <i class="bi bi-calendar-event" style="font-size: 2rem; opacity: 0.3;"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card stat-card bg-warning text-dark mb-4">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">EVENTOS ATIVOS</h6>
                    <h2 class="mb-0"><?= htmlspecialchars($active_events) ?></h2>
                  </div>
                  <i class="bi bi-calendar-check" style="font-size: 2rem; opacity: 0.3;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Events -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Eventos Recentes</h5>
          </div>
          <div class="card-body">
            <?php
            try {
                $sql_recent_events = "SELECT * FROM eventos ORDER BY data DESC LIMIT 5";
                $result_recent_events = $conn->query($sql_recent_events);
                
                if ($result_recent_events && $result_recent_events->num_rows > 0):
                ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($evento = $result_recent_events->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($evento['titulo']) ?></td>
                        <td><?= date('d/m/Y', strtotime($evento['data'])) ?></td>
                        <td><?= ucfirst(htmlspecialchars($evento['tipo'])) ?></td>
                        <td>
                          <a href="editar_evento.php?id=<?= $evento['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                        </td>
                      </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
                <?php else: ?>
                  <p>Nenhum evento encontrado.</p>
                <?php endif;
            } catch (Exception $e) {
                error_log("Erro ao carregar eventos recentes: " . $e->getMessage());
                echo '<p class="text-danger">Ocorreu um erro ao carregar os eventos recentes.</p>';
            }
            ?>
          </div>
        </div>
      </main>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>
</html>