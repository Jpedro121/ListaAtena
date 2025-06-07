<?php
require '../includes/check_admin.php';
require '../includes/db.php';

// Processar ações (ativar/desativar)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($_GET['action'] === 'toggle') {
        $stmt = $conn->prepare("UPDATE utilizadores SET ativo = NOT ativo WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($_GET['action'] === 'delete' && $id != $_SESSION['id']) {
        $stmt = $conn->prepare("DELETE FROM utilizadores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    
    header('Location: utilizadores.php');
    exit;
}

// Obter todos os utilizadores
$sql = "SELECT * FROM utilizadores ORDER BY data_registo DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Gestão de Utilizadores</title>
  <?php include '../includes/head.html'; ?>
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

        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Registado em</th>
                <th>Estado</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($user = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($user['nome']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                  <span class="badge bg-<?= $user['tipo'] === 'admin' ? 'warning' : 'primary' ?>">
                    <?= ucfirst($user['tipo']) ?>
                  </span>
                </td>
                <td><?= date('d/m/Y', strtotime($user['data_registo'])) ?></td>
                <td>
                  <span class="badge bg-<?= $user['ativo'] ? 'success' : 'danger' ?>">
                    <?= $user['ativo'] ? 'Ativo' : 'Inativo' ?>
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="editar_utilizador.php?id=<?= $user['id'] ?>" class="btn btn-secondary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="utilizadores.php?action=toggle&id=<?= $user['id'] ?>" 
                       class="btn btn-<?= $user['ativo'] ? 'warning' : 'success' ?>">
                      <i class="bi bi-power"></i>
                    </a>
                    <?php if ($user['id'] != $_SESSION['id']): ?>
                    <a href="utilizadores.php?action=delete&id=<?= $user['id'] ?>" 
                       class="btn btn-danger" onclick="return confirm('Tem certeza?')">
                      <i class="bi bi-trash"></i>
                    </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>
</html>