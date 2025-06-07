<?php
require '../includes/db.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

// Verifica se é email da escola
if (!preg_match("/@esjs-mafra\.net$/", $email)) {
  die("Apenas e-mails da escola (@esjs-mafra.net) são permitidos.");
}

// Verifica se já existe
$sql = "SELECT id FROM utilizadores WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
  die("Este e-mail já está registado.");
}

// Adicione na inserção
$username = $_POST['username'];
$sql = "INSERT INTO utilizadores (nome, username, email, senha, tipo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nome, $username, $email, $senha_hash, $tipo);

// Inserir na BD
$sql = "INSERT INTO utilizadores (nome, email, senha, tipo) VALUES (?, ?, ?, 'aluno')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha);
$stmt->execute();

// Enviar email (simples com mail())
$assunto = "Bem-vindo à Associação de Estudantes!";
$mensagem = "Olá $nome,\n\nObrigado por te registares no site da Associação de Estudantes da ESJS.\n\nCumprimentos,\nAE ESJS";
$headers = "From: associacao@esjs-mafra.net";

mail($email, $assunto, $mensagem, $headers);

// Redireciona para login
header("Location: login.php?registado=1");
exit;
?>
