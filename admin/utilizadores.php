<?php
require '../includes/check_admin.php';
require '../includes/db.php';

if ($_SESSION['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Action processing
if (isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        if ($_GET['action'] === 'toggle') {
            $stmt = $conn->prepare("UPDATE utilizadores SET ativo = NOT ativo WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Estado do utilizador alterado com sucesso!'
            ];
            
        } elseif ($_GET['action'] === 'delete' && $id != $_SESSION['id']) {
            $check_events = $conn->prepare("SELECT COUNT(*) FROM eventos WHERE id_organizador = ?");
            $check_events->bind_param("i", $id);
            $check_events->execute();
            $has_events = $check_events->get_result()->fetch_row()[0];
            
            if ($has_events > 0) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Não é possível excluir - o utilizador tem eventos associados!'
                ];
            } else {
                $stmt = $conn->prepare("DELETE FROM utilizadores WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Utilizador excluído com sucesso!'
                ];
            }
        }
        
        header('Location: utilizadores.php');
        exit();
        
    } catch (Exception $e) {
        error_log("Erro ao processar ação: " . $e->getMessage());
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Ocorreu um erro ao processar a ação!'
        ];
        header('Location: utilizadores.php');
        exit();
    }
}

// Filtros e pesquisa
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';

// Construir a query base
$sql_where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $sql_where[] = "(nome LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($status_filter !== 'all') {
    $sql_where[] = "ativo = ?";
    $params[] = ($status_filter === 'active') ? 1 : 0;
    $types .= 'i';
}

if ($type_filter !== 'all') {
    $sql_where[] = "tipo = ?";
    $params[] = $type_filter;
    $types .= 's';
}

// Obter todos os utilizadores com paginação e filtros
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Query para contar total
$sql_count = "SELECT COUNT(*) FROM utilizadores";
if (!empty($sql_where)) {
    $sql_count .= " WHERE " . implode(" AND ", $sql_where);
}
$stmt_count = $conn->prepare($sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_users = $stmt_count->get_result()->fetch_row()[0];
$total_pages = ceil($total_users / $limit);

// Query para obter os dados
$sql = "SELECT * FROM utilizadores";
if (!empty($sql_where)) {
    $sql .= " WHERE " . implode(" AND ", $sql_where);
}
$sql .= " ORDER BY data_registo DESC LIMIT ? OFFSET ?";
$types .= 'ii';
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Gestão de Utilizadores | Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #001f3f; /* Navy blue color */
      --secondary-color: #858796;
      --success-color: #1cc88a;
      --danger-color: #e74a3b;
      --warning-color: #f6c23e;
      --info-color: #36b9cc;
      --dark-color: #5a5c69;
      --light-color: #f8f9fc;
    }
    
    body {
      background-color: #f8f9fc;
    }
    
    .sidebar {
      background: var(--primary-color);
      min-height: 100vh;
      padding: 20px 0;
    }
    
    .sidebar .nav-link {
      color: rgba(255,255,255,0.8);
      padding: 10px 15px;
      margin: 2px 10px;
      border-radius: 4px;
    }
    
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active {
      background: rgba(255,255,255,0.1);
      color: white;
    }
    
    .sidebar .nav-link i {
      margin-right: 8px;
    }
    
    .main-content {
      padding: 20px;
    }
    
    .card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      margin-bottom: 1.5rem;
    }
    
    .card-header {
      background-color: white;
      border-bottom: 1px solid #e3e6f0;
      padding: 1.25rem 1.5rem;
    }
    
    .table-responsive {
      border-radius: 8px;
      overflow: hidden;
    }
    
    .table thead {
      background-color: var(--primary-color);
      color: white;
    }
    
    .table th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      padding: 1rem;
      vertical-align: middle;
    }
    
    .table td {
      padding: 0.75rem 1rem;
      vertical-align: middle;
      border-top: 1px solid #e3e6f0;
    }
    
    .badge-admin {
      background-color: var(--warning-color);
      color: #212529;
    }
    
    .badge-user {
      background-color: var(--info-color);
      color: white;
    }
    
    .action-btn {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50% !important;
      margin: 0 2px;
    }
    
    .pagination .page-item.active .page-link {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .pagination .page-link {
      color: var(--primary-color);
    }
    
    .search-box {
      position: relative;
    }
    
    .search-box .form-control {
      padding-left: 2.5rem;
    }
    
    .search-box .bi-search {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #d1d3e2;
    }
    
    .filter-select {
      min-width: 120px;
    }
    
    .breadcrumb {
      background-color: transparent;
      padding: 0;
      font-size: 0.85rem;
    }
    
    .no-results {
      padding: 3rem 0;
      text-align: center;
    }
    
    .no-results-icon {
      font-size: 3rem;
      color: #dddfeb;
      margin-bottom: 1rem;
    }
    
    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-none d-md-block sidebar">
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <i class="bi bi-speedometer2"></i> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="eventos_admin.php">
                <i class="bi bi-calendar-event"></i> Eventos
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="utilizadores.php">
                <i class="bi bi-people"></i> Utilizadores
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../perfil.php">
                <i class="bi bi-person"></i> Meu Perfil
              </a>
            </li>
          </ul>
        </div>
      </nav>
      
      <!-- Main Content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 card-header">
          <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-people-fill me-2"></i>Gestão de Utilizadores</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Utilizadores</li>
              </ol>
            </nav>
          </div>
          <div class="btn-toolbar mb-2 mb-md-0">
            <a href="adicionar_utilizador.php" class="btn btn-primary">
              <i class="bi bi-plus-lg me-1"></i> Adicionar
            </a>
          </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
            <div class="d-flex align-items-center">
              <i class="bi <?= $_SESSION['message']['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
              <div><?= $_SESSION['message']['text'] ?></div>
              <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="card mb-4">
          <div class="card-body p-3">
            <form method="get" class="row g-3 align-items-end">
              <div class="col-md-4">
                <label for="search" class="form-label">Pesquisar</label>
                <div class="search-box">
                  <i class="bi bi-search"></i>
                  <input type="text" class="form-control" id="search" name="search" placeholder="Nome ou email" value="<?= htmlspecialchars($search) ?>">
                </div>
              </div>
              <div class="col-md-3">
                <label for="status" class="form-label">Estado</label>
                <select id="status" name="status" class="form-select filter-select">
                  <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Todos</option>
                  <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Ativos</option>
                  <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inativos</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="type" class="form-label">Tipo</label>
                <select id="type" name="type" class="form-select filter-select">
                  <option value="all" <?= $type_filter === 'all' ? 'selected' : '' ?>>Todos</option>
                  <option value="admin" <?= $type_filter === 'admin' ? 'selected' : '' ?>>Administradores</option>
                  <option value="user" <?= $type_filter === 'user' ? 'selected' : '' ?>>Utilizadores</option>
                </select>
              </div>
              <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                <?php if ($search || $status_filter !== 'all' || $type_filter !== 'all'): ?>
                  <a href="utilizadores.php" class="btn btn-outline-secondary ms-2" title="Limpar filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Registado em</th>
                    <th>Estado</th>
                    <th class="text-end">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="user-avatar">
                            <?= strtoupper(substr($user['nome'], 0, 1)) ?>
                          </div>
                          <div class="flex-grow-1">
                            <?= htmlspecialchars($user['nome']) ?>
                            <?php if ($user['id'] == $_SESSION['id']): ?>
                              <span class="badge bg-primary ms-1">Você</span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </td>
                      <td><?= htmlspecialchars($user['email']) ?></td>
                      <td>
                        <span class="badge rounded-pill <?= $user['tipo'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                          <?= ucfirst(htmlspecialchars($user['tipo'])) ?>
                        </span>
                      </td>
                      <td><?= date('d/m/Y H:i', strtotime($user['data_registo'])) ?></td>
                      <td>
                        <span class="badge rounded-pill bg-<?= $user['ativo'] ? 'success' : 'danger' ?>">
                          <?= $user['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <div class="d-flex justify-content-end">
                          <a href="editar_utilizador.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary action-btn" title="Editar">
                            <i class="bi bi-pencil"></i>
                          </a>
                          <a href="utilizadores.php?action=toggle&id=<?= $user['id'] ?>" 
                             class="btn btn-sm btn-outline-<?= $user['ativo'] ? 'warning' : 'success' ?> action-btn" 
                             title="<?= $user['ativo'] ? 'Desativar' : 'Ativar' ?>">
                            <i class="bi bi-power"></i>
                          </a>
                          <?php if ($user['id'] != $_SESSION['id']): ?>
                          <a href="utilizadores.php?action=delete&id=<?= $user['id'] ?>" 
                             class="btn btn-sm btn-outline-danger action-btn" 
                             title="Excluir" 
                             onclick="return confirm('Tem certeza que deseja excluir este utilizador permanentemente?')">
                            <i class="bi bi-trash"></i>
                          </a>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="no-results">
                        <div class="no-results-icon">
                          <i class="bi bi-people"></i>
                        </div>
                        <h5 class="text-muted">Nenhum utilizador encontrado</h5>
                        <p class="text-muted mb-4">Tente ajustar seus filtros de pesquisa</p>
                        <a href="adicionar_utilizador.php" class="btn btn-primary">
                          <i class="bi bi-plus-lg me-1"></i> Adicionar Utilizador
                        </a>
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            
            <?php 
            // Mostrar apenas páginas próximas da atual
            $start = max(1, $page - 2);
            $end = min($total_pages, $page + 2);
            
            if ($start > 1): ?>
              <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a>
              </li>
              <?php if ($start > 2): ?>
                <li class="page-item disabled">
                  <span class="page-link">...</span>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            
            <?php if ($end < $total_pages): ?>
              <?php if ($end < $total_pages - 1): ?>
                <li class="page-item disabled">
                  <span class="page-link">...</span>
                </li>
              <?php endif; ?>
              <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>"><?= $total_pages ?></a>
              </li>
            <?php endif; ?>
            
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Próxima">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Manter filtros na paginação
    document.querySelectorAll('.pagination a').forEach(link => {
      const url = new URL(link.href);
      const params = new URLSearchParams(url.search);
      
      // Adiciona filtros atuais se não estiverem presentes
      if (!params.has('search') && '<?= $search ?>') {
        params.set('search', '<?= $search ?>');
      }
      if (!params.has('status') && '<?= $status_filter ?>' !== 'all') {
        params.set('status', '<?= $status_filter ?>');
      }
      if (!params.has('type') && '<?= $type_filter ?>' !== 'all') {
        params.set('type', '<?= $type_filter ?>');
      }
      
      link.href = url.pathname + '?' + params.toString();
    });
  </script>
</body>
</html>