<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/login.php?erro=5');
    exit;
}

require 'includes/db.php';

// Tratamento de erros para a conexão com o banco
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Buscar eventos
$upcomingEvents = [];
$sql = "SELECT id, titulo, data, hora, descricao, imagem FROM eventos WHERE data >= CURDATE() ORDER BY data ASC LIMIT 3";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $upcomingEvents = $result->fetch_all(MYSQLI_ASSOC);
    }
    $result->free();
} else {
    // Log de erro em produção
    error_log("Erro na query: " . $conn->error);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eventos</title>
  <?php include 'includes/head.html'; ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <style>
    .fc-event { 
      cursor: pointer; 
      font-size: 0.85em;
      padding: 2px 4px;
    }
    .event-card { 
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      height: 100%;
    }
    .event-card:hover { 
      transform: translateY(-5px); 
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .calendar-loading {
      min-height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .no-events {
      padding: 2rem;
      text-align: center;
      background-color: #f8f9fa;
      border-radius: 0.25rem;
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container mt-4 mb-5">
    <h1 class="text-center mb-4">Eventos e Palestras</h1>
    
    <div class="card mb-5 border-0 shadow-sm">
      <div class="card-body p-0">
        <div id="calendar" class="calendar-loading">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
        </div>
      </div>
    </div>
    
    <h2 class="mb-4">Próximos Eventos</h2>
    <div class="row g-4">
      <?php if (!empty($upcomingEvents)): ?>
        <?php foreach ($upcomingEvents as $evento): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card event-card h-100">
            <?php if (!empty($evento['imagem'])): ?>
              <img src="static/eventos/<?= htmlspecialchars($evento['imagem']) ?>" 
                   class="card-img-top img-fluid" 
                   alt="<?= htmlspecialchars($evento['titulo']) ?>"
                   loading="lazy">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h3 class="card-title h5"><?= htmlspecialchars($evento['titulo']) ?></h3>
              <p class="card-text text-muted mb-2">
                <i class="bi bi-calendar-event me-1"></i>
                <?= date('d/m/Y', strtotime($evento['data'])) ?>
                <?php if (!empty($evento['hora'])): ?>
                  <span class="ms-2">
                    <i class="bi bi-clock me-1"></i>
                    <?= date('H:i', strtotime($evento['hora'])) ?>
                  </span>
                <?php endif; ?>
              </p>
              <div class="card-text mb-3 flex-grow-1">
                <?= nl2br(htmlspecialchars(substr($evento['descricao'], 0, 150))) ?>
                <?= strlen($evento['descricao']) > 150 ? '...' : '' ?>
              </div>
              <a href="detalhes_evento.php?id=<?= $evento['id'] ?>" 
                 class="btn btn-primary align-self-start mt-auto">
                Ver Detalhes
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="no-events">
            <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
            <h3 class="h4">Nenhum evento programado</h3>
            <p class="text-muted">Volte mais tarde para conferir nossa programação.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      
      try {
        const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          locale: 'pt',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: {
            url: 'includes/get_events.php',
            failure: function() {
              calendarEl.innerHTML = '<div class="alert alert-danger">Falha ao carregar eventos. Por favor, recarregue a página.</div>';
            }
          },
          eventClick: function(info) {
            window.location.href = 'detalhes_evento.php?id=' + info.event.id;
          },
          eventDisplay: 'block',
          eventColor: '#001f3f',
          eventTextColor: '#ffffff',
          loading: function(isLoading) {
            if (!isLoading) {
              calendarEl.classList.remove('calendar-loading');
            }
          }
        });
        
        calendar.render();
      } catch (error) {
        console.error('Erro ao inicializar o calendário:', error);
        calendarEl.innerHTML = '<div class="alert alert-danger">O calendário não pôde ser carregado.</div>';
      }
    });
  </script>
</body>
</html>