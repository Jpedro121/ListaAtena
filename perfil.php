<?php
require 'includes/check_login.php';
require 'includes/db.php';

$sql = "SELECT * FROM utilizadores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Obter eventos do utilizador
$sql_events = "SELECT e.* FROM eventos e 
               JOIN inscricoes_eventos i ON e.id = i.id_evento 
               WHERE i.id_utilizador = ?";
$stmt_events = $conn->prepare($sql_events);
$stmt_events->bind_param("i", $_SESSION['id']);
$stmt_events->execute();
$events_result = $stmt_events->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Meu Perfil</title>
  <?php include 'includes/head.html'; ?>
  <style>
    /* Container principal mais largo */
    .container {
      max-width: 1400px;  /* Aumentei de 1200px para 1400px */
      width: 95%;         /* Ocupa 95% da tela */
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Colunas mais largas */
    .col-md-4 {
      flex: 0 0 40%;      /* Aumentei de 35% para 40% */
      max-width: 40%;
    }
    
    .col-md-8 {
      flex: 0 0 60%;      /* Reduzi de 65% para 60% */
      max-width: 60%;
    }

    .mb-0 {
      flex: 0 0 25%;      /* Coluna adicional para cards */
      max-width: 25%;
    }

    /* Cards mais largos */
    .info-card {
      width: 100%;
      min-width: 0;       /* Permite que o card diminua */
    }

    /* Formulários ocupando toda largura */
    .form-control {
      width: 100%;
      min-width: 0;
      height: 45px;       /* Campos de input mais altos */
      font-size: 1.05rem; /* Texto ligeiramente maior */
      padding: 10px 15px; /* Mais espaço interno */
    }

    /* Estilo específico para o card de alteração de senha */
    .card-body form .mb-3 {
      margin-bottom: 1.5rem !important; /* Mais espaço entre os campos */
    }

    .card-body form label {
      font-size: 1.05rem; /* Labels maiores */
      margin-bottom: 0.5rem; /* Mais espaço abaixo das labels */
    }

    /* Botão maior */
    .card-body form .btn {
      padding: 10px 20px;
      font-size: 1.05rem;
      height: 45px;
    }

    /* Ajuste para mobile */
    @media (max-width: 768px) {
      .container {
        max-width: 100%;
        padding: 0 15px;
      }
      
      .col-md-4,
      .col-md-8 {
        flex: 0 0 100%;   /* Empilha colunas em mobile */
        max-width: 100%;
      }

      /* Ajustes específicos para mobile */
      .form-control {
        height: 50px;     /* Campos ainda maiores em mobile para facilitar o toque */
        font-size: 16px;  /* Tamanho de fonte que evita zoom automático em iOS */
      }
      
      .card-body {
        padding: 1.5rem;  /* Mais padding no card em mobile */
      }
    }
  </style>

</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container py-4">
    <div class="profile-header text-center">
      <div class="d-flex justify-content-center mb-3">
        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
             style="width: 120px; height: 120px;">
          <i class="bi bi-person-fill" style="font-size: 3.5rem; color: #001f3f;"></i>
        </div>
      </div>
      <h2><?= htmlspecialchars($user['nome']) ?></h2>
      <p class="mb-0">
        <span class="badge bg-<?= $user['tipo'] === 'admin' ? 'warning text-dark' : 'primary' ?>">
          <?= ucfirst($user['tipo']) ?>
        </span>
      </p>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="card info-card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informações Pessoais</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <strong><i class="bi bi-envelope me-2"></i> Email:</strong>
                <p class="mt-1"><?= htmlspecialchars($user['email']) ?></p>
              </li>
              <li class="list-group-item">
                <strong><i class="bi bi-calendar me-2"></i> Registado em:</strong>
                <p class="mt-1"><?= date('d/m/Y H:i', strtotime($user['data_registo'])) ?></p>
              </li>
              <?php if ($user['ultimo_login']): ?>
              <li class="list-group-item">
                <strong><i class="bi bi-clock-history me-2"></i> Último login:</strong>
                <p class="mt-1"><?= date('d/m/Y H:i', strtotime($user['ultimo_login'])) ?></p>
              </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <div class="card info-card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Alterar Senha</h5>
          </div>
          <div class="card-body">
            <form id="changePasswordForm">
              <div class="mb-3">
                <label for="currentPassword" class="form-label">Senha Atual</label>
                <input type="password" class="form-control" id="currentPassword" required>
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">Nova Senha</label>
                <input type="password" class="form-control" id="newPassword" required>
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="confirmPassword" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Alterar Senha</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card info-card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Meus Eventos</h5>
          </div>
          <div class="card-body">
            <?php if ($events_result->num_rows > 0): ?>
              <div class="row">
                <?php while ($evento = $events_result->fetch_assoc()): ?>
                <div class="col-md-6 mb-3">
                  <div class="card event-card h-100">
                    <?php if ($evento['imagem']): ?>
                      <img src="static/eventos/<?= htmlspecialchars($evento['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($evento['titulo']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($evento['titulo']) ?></h5>
                      <p class="card-text">
                        <small class="text-muted">
                          <i class="bi bi-calendar-event"></i> <?= date('d/m/Y', strtotime($evento['data'])) ?>
                          <?php if ($evento['hora']): ?>
                            • <i class="bi bi-clock"></i> <?= date('H:i', strtotime($evento['hora'])) ?>
                          <?php endif; ?>
                        </small>
                      </p>
                      <a href="detalhes_evento.php?id=<?= $evento['id'] ?>" class="btn btn-primary btn-sm">Ver Detalhes</a>
                    </div>
                  </div>
                </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="text-center py-4">
                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d;"></i>
                <h5 class="mt-3">Não estás inscrito em nenhum evento</h5>
                <p class="text-muted">Descobre os nossos eventos e participa!</p>
                <a href="eventos.php" class="btn btn-primary">Explorar Eventos</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script>
    // Exemplo de validação de senha (implementar AJAX para enviar ao servidor)
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      
      if (newPassword !== confirmPassword) {
        alert('As senhas não coincidem!');
        return;
      }
      
      if (newPassword.length < 6) {
        alert('A senha deve ter pelo menos 6 caracteres');
        return;
      }
      
      // Aqui você implementaria a chamada AJAX para o servidor
      alert('Funcionalidade de alteração de senha será implementada aqui!');
      console.log('Senha atual:', currentPassword);
      console.log('Nova senha:', newPassword);
      
      // Limpar o formulário
      this.reset();
    });
  </script>
</body>
</html>
