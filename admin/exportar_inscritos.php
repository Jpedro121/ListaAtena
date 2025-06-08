<?php
session_start();

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit();
}

// Inclui o arquivo de conexão
require_once __DIR__ . '/../includes/db.php';

// Verificar se o ID do evento foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: eventos.php");
    exit();
}

$evento_id = intval($_GET['id']);

// Buscar informações do evento
$sql_evento = "SELECT titulo FROM eventos WHERE id = ?";
$stmt = $conn->prepare($sql_evento);
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$result = $stmt->get_result();
$evento = $result->fetch_assoc();

if (!$evento) {
    header("Location: eventos.php");
    exit();
}

// Buscar lista de inscritos
$sql = "SELECT u.nome, u.email, ie.data_inscricao 
        FROM inscricoes_eventos ie
        JOIN utilizadores u ON ie.id_utilizador = u.id
        WHERE ie.id_evento = ?
        ORDER BY ie.data_inscricao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$inscritos = $stmt->get_result();

// Configurar headers para download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inscritos_' . preg_replace('/[^a-z0-9]/i', '_', strtolower($evento['titulo'])) . '.csv');

// Criar arquivo CSV
$output = fopen('php://output', 'w');

// Escrever cabeçalho
fputcsv($output, ['Nome', 'Email', 'Data de Inscrição'], ';');

// Escrever dados
while ($row = $inscritos->fetch_assoc()) {
    fputcsv($output, [
        $row['nome'],
        $row['email'],
        date('d/m/Y H:i', strtotime($row['data_inscricao']))
    ], ';');
}

fclose($output);
exit();
?>