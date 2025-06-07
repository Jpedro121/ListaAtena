<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /login/login.php');
    exit;
}

// Atualizar último login
require 'db.php';
$stmt = $conn->prepare("UPDATE utilizadores SET ultimo_login = NOW() WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
?>