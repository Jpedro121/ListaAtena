<?php
require 'check_login.php';

// Verifica se o tipo de usuário está definido e é admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    // Registra tentativa de acesso não autorizado
    error_log("Tentativa de acesso admin não autorizado. Usuário ID: " . ($_SESSION['id'] ?? 'null'));
    
    header('Location: /index.php?error=unauthorized');
    exit;
}

// Verificação adicional para administradores (opcional)
if (!isset($_SESSION['is_admin_verified'])) {
    // Pode adicionar verificação extra no banco de dados se necessário
    require 'db.php';
    
    try {
        $stmt = $conn->prepare("SELECT 1 FROM utilizadores WHERE id = ? AND tipo = 'admin'");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        
        if (!$stmt->get_result()->fetch_assoc()) {
            error_log("Acesso admin fraudulento detectado. ID: " . $_SESSION['id']);
            session_unset();
            session_destroy();
            header('Location: /ListaAtena/login/login.php?error=admin_verification');
            exit;
        }
        
        $_SESSION['is_admin_verified'] = true;
        $stmt->close();
    } catch (Exception $e) {
        error_log("Erro na verificação de admin: " . $e->getMessage());
        // Continua normalmente em caso de falha na verificação
    }
}
?>