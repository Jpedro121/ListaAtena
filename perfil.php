<?php
require 'includes/check_login.php';
require 'includes/db.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/login.php?erro=5');
    exit;
}

// Obter dados do utilizador
$user = [];
$events = [];
$success_message = '';

try {
    // Dados do utilizador
    $stmt = $conn->prepare("SELECT id, nome, email, tipo, data_registo, ultimo_login, foto_perfil FROM utilizadores WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Eventos do utilizador
    $stmt = $conn->prepare("SELECT e.id, e.titulo, e.data, e.hora, e.imagem, e.local 
                           FROM eventos e 
                           JOIN inscricoes_eventos i ON e.id = i.id_evento 
                           WHERE i.id_utilizador = ? 
                           ORDER BY e.data ASC");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Alteração de senha
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = $conn->prepare("SELECT password FROM utilizadores WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        $db_password = $stmt->get_result()->fetch_assoc()['password'];

        if (!password_verify($current_password, $db_password)) {
            throw new Exception("Senha atual incorreta");
        }

        if ($new_password !== $confirm_password) {
            throw new Exception("As senhas não coincidem");
        }

        if (strlen($new_password) < 8) {
            throw new Exception("Senha deve ter 8+ caracteres");
        }

        $stmt = $conn->prepare("UPDATE utilizadores SET password = ? WHERE id = ?");
        $stmt->bind_param("si", password_hash($new_password, PASSWORD_DEFAULT), $_SESSION['id']);
        $stmt->execute();
        $success_message = "Senha alterada com sucesso!";
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil</title>
  <?php include 'includes/head.html'; ?>
  <link rel="stylesheet" href="css/perfil.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="profile-container py-4">
    <div class="profile-header text-center">
      <div class="d-flex justify-content-center mb-3 position-relative">
        <img src="<?= !empty($user['foto_perfil']) ? 'uploads/profiles/'.htmlspecialchars($user['foto_perfil']) : 'assets/default-profile.png' ?>" 
             class="profile-avatar" 
             alt="Foto de perfil">
        <button class="btn btn-sm btn-outline-primary position-absolute" 
                style="bottom: 0; right: calc(50% - 80px);" 
                data-bs-toggle="modal" 
                data-bs-target="#avatarModal">
          <i class="bi bi-camera"></i>
        </button>
      </div>
      <h1 class="h2 mb-2"><?= htmlspecialchars($user['nome']) ?></h1>
      <span class="badge bg-<?= $user['tipo'] === 'admin' ? 'warning text-dark' : 'primary' ?>">
        <?= ucfirst($user['tipo']) ?>
      </span>
    </div>

    <?php include 'includes/alerts.php'; ?>

    <div class="row">
      <div class="col-md-4">
        <?php include 'includes/profile_info_card.php'; ?>
        <?php include 'includes/profile_security_card.php'; ?>
      </div>
      
      <div class="col-md-8">
        <?php include 'includes/profile_events_card.php'; ?>
      </div>
    </div>
  </main>

  <?php include 'includes/avatar_modal.php'; ?>
  <?php include 'includes/footer.php'; ?>

  <script src="static/js/perfil.js"></script>
</body>
</html>