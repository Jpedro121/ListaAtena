<?php
session_start();

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: eventos.php");
    exit();
}

$evento_id = intval($_GET['id']);

// Iniciar transação
$conn->begin_transaction();

try {
    // 1. Remover todas as inscrições do evento
    $sql = "DELETE FROM inscricoes_eventos WHERE id_evento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    
    // 2. Remover o evento
    $sql = "DELETE FROM eventos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    
    $conn->commit();
    header("Location: eventos.php?success=Evento+excluído+com+sucesso");
} catch (Exception $e) {
    $conn->rollback();
    header("Location: eventos.php?error=Erro+ao+excluir+evento");
}

exit();
?>