<?php
require 'includes/check_login.php';
require 'includes/db.php';

// Verificação robusta de sessão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: login/login.php?erro=5');
    exit;
}

// No início do arquivo, após obter os dados do usuário
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Inicializa variáveis
$user = [
    'nome' => 'Não definido',
    'email' => 'Não definido',
    'tipo' => 'regular',
    'data_registo' => null,
    'ultimo_login' => null,
    'foto_perfil' => null
];
$events = [];
$error_message = '';
$success_message = '';

try {
    // Consulta segura com alias explícitos
  $stmt = $conn->prepare("SELECT 
    id, 
    nome, 
    email, 
    tipo, 
    data_registo, 
    ultimo_login,
    foto_perfil
    FROM utilizadores 
    WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = array_merge($user, $result->fetch_assoc());
    } else {
        throw new Exception("Usuário não encontrado");
    }

    // Consulta de eventos
    $stmt = $conn->prepare("SELECT e.id, e.titulo, e.data, e.hora, e.imagem, e.local 
                           FROM eventos e 
                           JOIN inscricoes_eventos i ON e.id = i.id_evento 
                           WHERE i.id_utilizador = ? 
                           ORDER BY e.data ASC");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error_message = "Erro ao carregar dados: " . $e->getMessage();
    error_log($error_message);
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
  <link rel="stylesheet" href="static/perfil.css">
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