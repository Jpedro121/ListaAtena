<?php
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Eventos</title>
  <?php include 'includes/head.html'; ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <style>
    .fc-event { cursor: pointer; }
    .event-card { transition: transform 0.3s; }
    .event-card:hover { transform: translateY(-5px); }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container mt-4">
    <h2 class="text-center mb-4">Eventos e Palestras</h2>
    
    <div id="calendar" class="mb-5"></div>
    
    <h3 class="mb-4">Próximos Eventos</h3>
    <div class="row">
      <?php
      $sql = "SELECT * FROM eventos WHERE data >= CURDATE() ORDER BY data ASC LIMIT 3";
      $result = $conn->query($sql);
      
      if ($result->num_rows > 0):
        while ($evento = $result->fetch_assoc()):
      ?>
      <div class="col-md-4 mb-4">
        <div class="card event-card h-100">
          <?php if ($evento['imagem']): ?>
            <img src="static/eventos/<?= htmlspecialchars($evento['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($evento['titulo']) ?>">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($evento['titulo']) ?></h5>
            <p class="card-text">
              <small class="text-muted">
                <?= date('d/m/Y', strtotime($evento['data'])) ?>
                <?php if ($evento['hora']): ?>
                  • <?= date('H:i', strtotime($evento['hora'])) ?>
                <?php endif; ?>
              </small>
            </p>
            <p class="card-text"><?= nl2br(htmlspecialchars(substr($evento['descricao'], 0, 100))) ?>...</p>
            <a href="detalhes_evento.php?id=<?= $evento['id'] ?>" class="btn btn-primary">Ver Mais</a>
          </div>
        </div>
      </div>
      <?php
        endwhile;
      else:
        echo '<div class="col-12"><p>Não há eventos programados.</p></div>';
      endif;
      ?>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt',
        events: 'includes/get_events.php',
        eventClick: function(info) {
          window.location.href = 'detalhes_evento.php?id=' + info.event.id;
        },
        eventColor: '#001f3f'
      });
      calendar.render();
    });
  </script>
</body>
</html>