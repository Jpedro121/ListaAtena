<?php
// Configurações de sessão segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => false, // Altere para true se estiver em HTTPS
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
        'gc_maxlifetime' => 86400
    ]);
}

// Verificação robusta de login com mais critérios
$session_invalid = (
    !isset($_SESSION['loggedin']) || 
    $_SESSION['loggedin'] !== true || 
    empty($_SESSION['id']) ||
    !isset($_SESSION['ultima_atividade']) ||
    (time() - $_SESSION['ultima_atividade'] > 3600)
);

if ($session_invalid) {
    error_log("Sessão inválida. IP: " . $_SERVER['REMOTE_ADDR']);
    session_unset();
    session_destroy();
    header('Location: /ListaAtena/login/login.php?error=invalid_session');
    exit;
}

// Atualiza tempo da última atividade
$_SESSION['ultima_atividade'] = time();

// Verificação de segurança (IP e user-agent)
$security_breach = (
    ($_SESSION['ip'] ?? null) !== $_SERVER['REMOTE_ADDR'] || 
    ($_SESSION['user_agent'] ?? null) !== ($_SERVER['HTTP_USER_AGENT'] ?? '')
);

if ($security_breach) {
    error_log("Violação de segurança detectada. IP: " . $_SERVER['REMOTE_ADDR']);
    session_unset();
    session_destroy();
    header('Location: /ListaAtena/login/login.php?error=security_breach');
    exit;
}

// Regeneração do ID de sessão a cada 30 minutos
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Atualiza o último login
require 'db.php';

try {
    $stmt = $conn->prepare("UPDATE utilizadores SET ultimo_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    error_log("Erro ao atualizar último login: " . $e->getMessage());
}
?>
