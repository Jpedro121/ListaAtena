<?php
require '../includes/db.php';  // Ajusta o caminho se necessário

session_start();

if (!isset($_GET['token'])) {
    $_SESSION['registo_erro'] = 'Token inválido ou ausente.';
    header('Location: login.php');
    exit;
}

$token_ativacao = $_GET['token'];
$token_hash = hash('sha256', $token_ativacao);

// Procura o utilizador com o token correto e que ainda não está ativo
$stmt = $conn->prepare("SELECT id, ativo FROM utilizadores WHERE token_ativacao = ? LIMIT 1");
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['registo_erro'] = 'Token inválido ou conta já ativada.';
    header('Location: login.php');
    exit;
}

$user = $result->fetch_assoc();

if ($user['ativo'] == 1) {
    $_SESSION['registo_erro'] = 'Conta já ativada.';
    header('Location: login.php');
    exit;
}

// Ativa a conta e remove o token
$stmt = $conn->prepare("UPDATE utilizadores SET ativo = 1, token_ativacao = NULL WHERE id = ?");
$stmt->bind_param("i", $user['id']);
if ($stmt->execute()) {
    $_SESSION['registo_sucesso'] = 'Conta ativada com sucesso! Já podes fazer login.';
    header('Location: login.php');
    exit;
} else {
    $_SESSION['registo_erro'] = 'Erro ao ativar a conta. Por favor tenta novamente.';
    header('Location: login.php');
    exit;
}
?>
