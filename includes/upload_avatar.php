<?php
session_start();
require 'db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado');
}

// Verifica se o arquivo foi enviado
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    header('HTTP/1.1 400 Bad Request');
    exit('Erro no upload do arquivo');
}

// Validações do arquivo
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
    header('HTTP/1.1 400 Bad Request');
    exit('Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.');
}

if ($_FILES['avatar']['size'] > $max_size) {
    header('HTTP/1.1 400 Bad Request');
    exit('O arquivo é muito grande. Tamanho máximo: 2MB.');
}

// Processa o upload
$upload_dir = '../uploads/profiles/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Gera um nome único para o arquivo
$file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$new_filename = 'avatar_' . $_SESSION['id'] . '_' . time() . '.' . $file_extension;
$destination = $upload_dir . $new_filename;

// Move o arquivo para o diretório de uploads
if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Falha ao salvar o arquivo.');
}

// Atualiza o banco de dados
try {
    $stmt = $conn->prepare("UPDATE utilizadores SET foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("si", $new_filename, $_SESSION['id']);
    $stmt->execute();
    
    // Atualiza a sessão se necessário
    $_SESSION['foto_perfil'] = $new_filename;
    
    // Redireciona de volta para o perfil com mensagem de sucesso
    $_SESSION['success_message'] = 'Foto de perfil atualizada com sucesso!';
    header('Location: ../perfil.php');
    exit;
    
} catch (Exception $e) {
    // Remove o arquivo se houve erro no banco de dados
    unlink($destination);
    header('HTTP/1.1 500 Internal Server Error');
    exit('Erro ao atualizar o perfil: ' . $e->getMessage());
}