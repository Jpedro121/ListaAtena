<?php
// Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'use_strict_mode' => true,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

// Registra o logout para auditoria
function registrarLogout($usuarioId) {
    // Implementação básica - adapte para seu sistema de logs
    $logMessage = date('Y-m-d H:i:s') . " - Usuário ID $usuarioId realizou logout";
    file_put_contents('../logs/acessos.log', $logMessage . PHP_EOL, FILE_APPEND);
}

// Prepara dados para redirecionamento
$redirectUrl = '/ListaAtena/login/login.php';
$mensagem = 'logout_sucesso';

// Se o usuário estava logado, registra o logout
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $usuarioId = $_SESSION['user_id'] ?? 'desconhecido';
    registrarLogout($usuarioId);
    
    // Adiciona mensagem para mostrar após redirecionamento
    $mensagem = 'logout_sucesso';
}

// Limpa todos os dados da sessão
$_SESSION = [];

// Invalida o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 86400,  // Expira 1 dia atrás
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona com mensagem de sucesso
header("Location: $redirectUrl?msg=" . urlencode($mensagem));
exit;
?>