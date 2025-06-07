<?php
// Configurações de sessão segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 dia
        'cookie_secure' => true,    // Apenas HTTPS
        'cookie_httponly' => true,  // Acesso apenas via HTTP
        'cookie_samesite' => 'Strict', // Proteção contra CSRF
        'use_strict_mode' => true,  // Prevenção de fixation
        'gc_maxlifetime' => 86400   // Tempo de vida do lixo coletor
    ]);
}

// Verificação robusta de login com mais critérios
$session_invalid = (
    !isset($_SESSION['loggedin']) || 
    $_SESSION['loggedin'] !== true || 
    empty($_SESSION['id']) ||
    !isset($_SESSION['last_activity']) ||
    (time() - $_SESSION['last_activity'] > 3600) // 1 hora de inatividade
);

if ($session_invalid) {
    // Registra tentativa de acesso inválido
    error_log("Tentativa de acesso com sessão inválida. IP: " . $_SERVER['REMOTE_ADDR']);
    
    session_unset();
    session_destroy();
    
    header('Location: /ListaAtena/login/login.php?error=invalid_session');
    exit;
}

// Atualiza tempo da última atividade
$_SESSION['last_activity'] = time();

// Verificação de segurança reforçada
$security_breach = (
    ($_SESSION['user_ip'] ?? null) !== $_SERVER['REMOTE_ADDR'] || 
    ($_SESSION['user_agent'] ?? null) !== $_SERVER['HTTP_USER_AGENT']
);

if ($security_breach) {
    error_log("Possível ataque de sessão detectado. IP: " . $_SERVER['REMOTE_ADDR']);
    
    session_unset();
    session_destroy();
    
    header('Location: /ListaAtena/login/login.php?error=security_breach');
    exit;
}

// Regeneração periódica do ID da sessão (a cada 30 minutos)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Atualização do último login com tratamento melhorado
require 'db.php';

try {
    // Prepara e executa a atualização
    $stmt = $conn->prepare("UPDATE utilizadores SET ultimo_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    
    if (!$stmt->execute()) {
        error_log("Falha ao atualizar último login para usuário ID: " . $_SESSION['id'] . 
                 " Erro: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log("Erro crítico ao atualizar último login: " . $e->getMessage() . 
             " [Usuário ID: " . ($_SESSION['id'] ?? 'null') . "]");
    
    // Não encerra a sessão por falha no log, apenas registra
}
?>