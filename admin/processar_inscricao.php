<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_evento'])) {
    $idEvento = (int)$_POST['id_evento'];
    $idUsuario = $_SESSION['id'];
    
    // Verificar se o evento existe
    $stmt = $conn->prepare("SELECT 1 FROM eventos WHERE id = ?");
    $stmt->bind_param("i", $idEvento);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Evento não encontrado'];
        header('Location: home.php');
        exit;
    }
    
    // Verificar se já está inscrito
    $stmt = $conn->prepare("SELECT 1 FROM inscricoes_eventos WHERE id_utilizador = ? AND id_evento = ?");
    $stmt->bind_param("ii", $idUsuario, $idEvento);
    $stmt->execute();
    $jaInscrito = $stmt->get_result()->num_rows > 0;

    if (isset($_POST['inscrever'])) {
        if (!$jaInscrito) {
            $stmt = $conn->prepare("INSERT INTO inscricoes_eventos (id_utilizador, id_evento) VALUES (?, ?)");
            $stmt->bind_param("ii", $idUsuario, $idEvento);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Inscrição realizada com sucesso!'];
            }
        }
    } elseif (isset($_POST['cancelar'])) {
        if ($jaInscrito) {
            $stmt = $conn->prepare("DELETE FROM inscricoes_eventos WHERE id_utilizador = ? AND id_evento = ?");
            $stmt->bind_param("ii", $idUsuario, $idEvento);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Inscrição cancelada com sucesso!'];
            }
        }
    }
    
    header("Location: ../detalhes_evento.php?id=$idEvento");
    exit;
}

header('Location: home.php');