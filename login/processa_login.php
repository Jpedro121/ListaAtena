<?php
require '../includes/db.php';

if (empty($_POST['login']) || empty($_POST['senha'])) {
    header('Location: login.php?erro=2');
    exit;
}

$login = $_POST['login'];
$senha = $_POST['senha'];

// Verifica se é email ou username
$isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

$sql = "SELECT id, nome, senha, tipo, ativo FROM utilizadores WHERE " . 
       ($isEmail ? "email = ?" : "username = ?");
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (!$user['ativo']) {
        header('Location: login.php?erro=3');
        exit;
    }
    
    if (password_verify($senha, $user['senha'])) {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['tipo'] = $user['tipo'];
        
        // Atualiza último login
        $update_sql = "UPDATE utilizadores SET ultimo_login = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $user['id']);
        $update_stmt->execute();
        
        header('Location: ../perfil.php');
        exit;
    }
}

header('Location: login.php?erro=1');
exit;
?>