<?php
session_start();

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit();
}

// Inclui o arquivo de conexão
require_once __DIR__ . '/../includes/db.php';

// Verificar se os parâmetros foram passados
if (!isset($_GET['evento_id']) || !isset($_GET['usuario_id'])) {
    header("Location: eventos.php");
    exit();
}

$evento_id = intval($_GET['evento_id']);
$usuario_id = intval($_GET['utilizador_id']);

// Remover a inscrição
$sql = "DELETE FROM inscricoes_eventos WHERE id_evento = ? AND id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $evento_id, $usuario_id);

if ($stmt->execute()) {
    // Redirecionar com mensagem de sucesso
    header("Location: admin/editar_evento.php?id=$evento_id&success=1");
} else {
    // Redirecionar com mensagem de erro
    header("Location: admin/editar_evento.php?id=$evento_id&error=1");
}
exit();
?>