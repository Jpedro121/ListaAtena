<?php
session_start();

require '../includes/db.php';

// Proteção contra CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: login.php?erro=5');
    exit;
}

// Verifica se login e senha foram enviados
if (empty($_POST['login']) || empty($_POST['senha'])) {
    header('Location: login.php?erro=2');
    exit;
}

// Sanitiza entrada
$login = trim($_POST['login']);
$senha = $_POST['senha'];
$lembrar = isset($_POST['lembrar']) && $_POST['lembrar'] === 'on';

// Função para registrar tentativas
function registrarTentativaLogin($login, $sucesso, $conn) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $sucessoInt = $sucesso ? 1 : 0;

    $sql = "INSERT INTO login_tentativas (username, sucesso, ip) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Erro na preparação do statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param("sis", $login, $sucessoInt, $ip);
    $stmt->execute();
    $stmt->close();
}

// Verifica se é email ou username
$isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

// Prepara query para buscar usuário
$sql = "SELECT id, nome, senha, tipo, ativo, tentativas_login, bloqueado_ate 
        FROM utilizadores WHERE " . ($isEmail ? "email = ?" : "username = ?");
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Erro na preparação da query: " . $conn->error);
    header('Location: login.php?erro=1');
    exit;
}

$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifica bloqueio temporário
    if ($user['bloqueado_ate'] && strtotime($user['bloqueado_ate']) > time()) {
        registrarTentativaLogin($login, false, $conn);
        header('Location: login.php?erro=6');
        exit;
    }

    // Verifica se conta está ativa
    if (!$user['ativo']) {
        registrarTentativaLogin($login, false, $conn);
        header('Location: login.php?erro=3');
        exit;
    }

    // Verifica senha
    if (password_verify($senha, $user['senha'])) {
        // Rehash se necessário
        if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($senha, PASSWORD_DEFAULT);
            $update_sql = "UPDATE utilizadores SET senha = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $newHash, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();
        }

        // Reseta tentativas e atualiza último login
        $update_sql = "UPDATE utilizadores SET tentativas_login = 0, ultimo_login = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $user['id']);
        $update_stmt->execute();
        $update_stmt->close();

        // Regenera sessão
        session_regenerate_id(true);

        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['nome'] = htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8');
        $_SESSION['tipo'] = $user['tipo'];
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
        $_SESSION['ultima_atividade'] = time();
        $_SESSION['created'] = time(); 


        // Cookie lembrar-me
        if ($lembrar) {
            $token = bin2hex(random_bytes(32));
            $hashToken = hash('sha256', $token);
            $expira = time() + 60 * 60 * 24 * 30; // 30 dias

            $sql = "INSERT INTO auth_tokens (user_id, token_hash, expira_em) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user['id'], $hashToken, date('Y-m-d H:i:s', $expira));
            $stmt->execute();
            $stmt->close();

            setcookie(
                'lembrar_token',
                $token,
                $expira,
                '/',
                '',
                false, // true se HTTPS
                true
            );
        }

        registrarTentativaLogin($login, true, $conn);
        header('Location: ../home.php');
        exit;

    } else {
        // Senha errada — incrementa tentativas
        $tentativas = $user['tentativas_login'] + 1;
        $bloqueado_ate = null;

        if ($tentativas >= 5) {
            $bloqueado_ate = date('Y-m-d H:i:s', time() + 900); // bloqueia 15 min
        }

        $update_sql = "UPDATE utilizadores SET tentativas_login = ?, bloqueado_ate = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isi", $tentativas, $bloqueado_ate, $user['id']);
        $update_stmt->execute();
        $update_stmt->close();

        registrarTentativaLogin($login, false, $conn);
    }
}

// Erro genérico
header('Location: login.php?erro=1');
exit;
