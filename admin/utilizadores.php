<?php
require '../includes/check_admin.php';
require '../includes/db.php';

// Verificação adicional de permissões
if ($_SESSION['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Processar ações (ativar/desativar)
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
            // Verificar se o usuário não está associado a eventos antes de deletar
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

// Obter todos os utilizadores com paginação
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(*) FROM utilizadores";
$total_users = $conn->query($sql_count)->fetch_row()[0];
$total_pages = ceil($total_users / $limit);

$sql = "SELECT * FROM utilizadores ORDER BY data_registo DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Gestão de Utilizadores</title>
  <?php include '../includes/head.html'; ?>
  <style>
    .badge-admin {
      background-color: #ffc107;
      color: #212529;
    }
    .badge-user {
      background-color: #0d6efd;
      color: white;
    }
    .action-buttons {
      min-width: 150px;
    }
    .pagination .page-item.active .page-link {
      background-color: #001f3f;
      border-color: #001f3f;
    }
    .pagination .page-link {
      color: #001f3f;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="container-fluid">
    <div class="row">
      <?php include 'sidebar.php'; ?>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Gestão de Utilizadores</h1>
          <a href="adicionar_utilizador.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Adicionar Utilizador
          </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
            <?= $_SESSION['message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Registado em</th>
                <th>Estado</th>
                <th class="action-buttons">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($user['nome']) ?></td>
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
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="editar_utilizador.php?id=<?= $user['id'] ?>" class="btn btn-outline-secondary" title="Editar">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="utilizadores.php?action=toggle&id=<?= $user['id'] ?>" 
                         class="btn btn-outline-<?= $user['ativo'] ? 'warning' : 'success' ?>" title="<?= $user['ativo'] ? 'Desativar' : 'Ativar' ?>">
                        <i class="bi bi-power"></i>
                      </a>
                      <?php if ($user['id'] != $_SESSION['id']): ?>
                      <a href="utilizadores.php?action=delete&id=<?= $user['id'] ?>" 
                         class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este utilizador?')">
                        <i class="bi bi-trash"></i>
                      </a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Nenhum utilizador encontrado</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center mt-4">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Próxima</a>
            </li>
          </ul>
        </nav>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>
</html>