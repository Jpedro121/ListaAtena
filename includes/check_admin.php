<?php
require 'check_login.php';

if ($_SESSION['tipo'] !== 'admin') {
    header('Location: /index.php');
    exit;
}
?>