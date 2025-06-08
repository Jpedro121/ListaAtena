<?php
session_start();
require '../includes/db.php';

// Importa PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';  // Ajusta o caminho se necessário

// Função para redirecionar com erro
function redirectWithError($message, $field = '') {
    $_SESSION['registo_erro'] = $message;
    if ($field) $_SESSION['registo_campos'][$field] = $_POST[$field] ?? '';
    header('Location: registar.php');
    exit;
}

// Validação dos campos
$requiredFields = ['nome', 'email', 'username', 'senha', 'confirmar_senha'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        redirectWithError('Por favor, preencha todos os campos.', $field);
    }
}

$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$username = trim($_POST['username']);
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];

// Valida email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('Por favor, insira um email válido.', 'email');
}
if (!preg_match("/@esjs-mafra\.net$/i", $email) && $email !== 'admin@gmail.com') {
    redirectWithError('Apenas emails da escola (@esjs-mafra.net) são permitidos, exceto o admin.', 'email');
}

// Valida username
if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
    redirectWithError('Username inválido. Use 4-20 caracteres, letras, números e _.', 'username');
}

// Valida senha
if (strlen($senha) < 8) {
    redirectWithError('A senha deve ter no mínimo 8 caracteres.', 'senha');
}
if ($senha !== $confirmar_senha) {
    redirectWithError('As senhas não coincidem.', 'senha');
}

// Verifica se email já existe
$stmt = $conn->prepare("SELECT id FROM utilizadores WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    redirectWithError('Este email já está registado.', 'email');
}

// Verifica se username já existe
$stmt = $conn->prepare("SELECT id FROM utilizadores WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    redirectWithError('Este username já está em uso.', 'username');
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
$token_ativacao = bin2hex(random_bytes(32));
$token_hash = hash('sha256', $token_ativacao);

// Determine user type and activation status
$tipo = ($email === 'admin@gmail.com') ? 'admin' : 'aluno';
$ativada = ($email === 'admin@gmail.com') ? 1 : 0;

$conn->begin_transaction();

try {
    $sql = "INSERT INTO utilizadores (nome, username, email, senha, token_ativacao, tipo, conta_ativada) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nome, $username, $email, $senha_hash, $token_hash, $tipo, $ativada);
    $stmt->execute();

    $user_id = $conn->insert_id;

    // Log registo
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql_log = "INSERT INTO registo_logs (user_id, ip, data_registo) VALUES (?, ?, NOW())";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("is", $user_id, $ip);
    $stmt_log->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Erro no registo: " . $e->getMessage());
    redirectWithError('Ocorreu um erro no registo. Por favor, tente novamente.');
}

// Only send activation email if not admin
if ($email !== 'admin@gmail.com') {
    // Envio de email com PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'joaopedroantunes1980@gmail.com';
        $mail->Password = 'keqh clrc lzie dsei';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('joaopedroantunes1980@gmail.com', 'ListaAtena');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Ativação da conta - ListaAtena';

        $activation_link = "http://" . $_SERVER['HTTP_HOST'] . "/ListaAtena/login/ativar_conta.php?token=$token_ativacao";

        $mail->Body = "
            <h1>Bem-vindo à Lista Atena !</h1>
            <p>Olá $nome,</p>
            <p>Obrigado por te registares no site da Lista Atena.</p>
            <p>Para ativar a tua conta, por favor clica no link abaixo:</p>
            <p><a href='$activation_link'>Ativar Conta</a></p>
            <p>Se não foste tu a criar esta conta, por favor ignora este email.</p>
            <p>Cumprimentos,</p>
            <p>AE ESJS</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $mail->ErrorInfo);
        // não bloqueia o registo se o email falhar
    }
}

// Limpa sessão e redireciona
unset($_SESSION['registo_erro'], $_SESSION['registo_campos']);
$_SESSION['registo_sucesso'] = 'Registo realizado com sucesso! ' . 
    ($email === 'admin@gmail.com' ? 'Conta de administrador ativada automaticamente.' : 'Por favor verifica o teu email para ativar a conta.');
header('Location: login.php');
exit;