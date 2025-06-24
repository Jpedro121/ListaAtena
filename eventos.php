<?php
// Mostrar mensagens de sucesso/erro
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            '.htmlspecialchars(urldecode($_GET['message'])).'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.htmlspecialchars(urldecode($_GET['message'])).'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

date_default_timezone_set('Europe/Lisbon');

require 'includes/db.php';

// Buscar todos os eventos (para o calendário)
$allEvents = [];
$sqlAll = "SELECT id, titulo, data, hora, descricao, tipo FROM eventos WHERE data >= CURDATE() ORDER BY data ASC";
$resultAll = $conn->query($sqlAll);

if ($resultAll && $resultAll->num_rows > 0) {
    $allEvents = $resultAll->fetch_all(MYSQLI_ASSOC);
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
    #calendar-container {
      max-width: 600px;
      margin: 0 auto 30px;
    }

    #calendar {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 10px;
    }

    .fc-day-palestra {
      background-color: rgba(40, 167, 69, 0.1);
      border-bottom: 3px solid #28a745 !important;
    }

    .fc-day-evento {
      background-color: rgba(220, 53, 69, 0.1);
      border-bottom: 3px solid #dc3545 !important;
    }

    .fc-day-workshop {
      background-color: rgba(255, 193, 7, 0.1);
      border-bottom: 3px solid #ffc107 !important;
    }

    .fc-day-today {
      background-color: rgba(0, 123, 255, 0.1) !important;
    }

    #event-details {
      max-width: 600px;
      margin: 0 auto 30px;
      display: none;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px;
      animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .fc .fc-toolbar-title {
      font-size: 1.2em;
    }

    .fc .fc-col-header-cell-cushion {
      font-size: 0.8em;
      padding: 2px 4px;
    }

    .fc .fc-daygrid-day-number {
      font-size: 0.9em;
      padding: 4px;
    }

    .fc .fc-button {
      padding: 0.3em 0.6em;
      font-size: 0.9em;
    }
    
    .event-badge {
      font-size: 0.8em;
      padding: 0.25em 0.4em;
      border-radius: 0.25rem;
    }
    
    .badge-palestra {
      background-color: #28a745;
      color: white;
    }
    
    .badge-evento {
      background-color: #dc3545;
      color: white;
    }
    
    .badge-workshop {
      background-color: #ffc107;
      color: #212529;
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container mt-4 mb-5">
    <h1 class="text-center mb-4">Calendário de Eventos</h1>

    <div id="calendar-container">
      <div id="calendar"></div>
      <div id="event-details">
        <h4 id="event-title" class="mb-3"></h4>
        <div id="event-content"></div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const eventDetails = document.getElementById('event-details');
      const eventTitle = document.getElementById('event-title');
      const eventContent = document.getElementById('event-content');

      const eventsData = [
        <?php foreach($allEvents as $event): ?>
        {
          id: '<?= $event['id'] ?>',
          title: '<?= addslashes($event['titulo']) ?>',
          start: '<?= $event['data'] ?>',
          tipo: '<?= $event['tipo'] ?>',
          descricao: `<?= addslashes(nl2br($event['descricao'])) ?>`,
          hora: '<?= $event['hora'] ? date('H:i', strtotime($event['hora'])) : '' ?>'
        },
        <?php endforeach; ?>
      ];

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt',
        timeZone: 'local',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: ''
        },
        dayCellClassNames: function(arg) {
          const cellDate = new Date(arg.date);
          const dayEvents = eventsData.filter(event => {
            const eventDate = new Date(event.start);
            return eventDate.getFullYear() === cellDate.getFullYear() &&
                   eventDate.getMonth() === cellDate.getMonth() &&
                   eventDate.getDate() === cellDate.getDate();
          });
          
          if (dayEvents.length > 0) {
            return 'fc-day-' + dayEvents[0].tipo;
          }
          return '';
        },
        dateClick: function(info) {
          const clickedDate = new Date(info.dateStr);
          const dayEvents = eventsData.filter(event => {
            const eventDate = new Date(event.start);
            return eventDate.getFullYear() === clickedDate.getFullYear() &&
                   eventDate.getMonth() === clickedDate.getMonth() &&
                   eventDate.getDate() === clickedDate.getDate();
          });

          if (dayEvents.length > 0) {
            const event = dayEvents[0];
            eventTitle.textContent = event.title;

            // Format the date in Portuguese format
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = new Date(event.start).toLocaleDateString('pt-PT', options);
            
            let detailsHTML = `
              <p><strong>Tipo:</strong> <span class="event-badge badge-${event.tipo}">${event.tipo.charAt(0).toUpperCase() + event.tipo.slice(1)}</span></p>
              <p><strong>Data:</strong> ${formattedDate}</p>
            `;

            if (event.hora) {
              detailsHTML += `<p><strong>Hora:</strong> ${event.hora}</p>`;
            }

            detailsHTML += `
              <div class="mt-3">${event.descricao}</div>
              <div class="mt-3">
                <a href="detalhes_evento.php?id=${event.id}" class="btn btn-primary btn-sm">
                  Ver mais detalhes
                </a>
              </div>
            `;

            eventContent.innerHTML = detailsHTML;
            eventDetails.style.display = 'block';
          } else {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = new Date(info.dateStr).toLocaleDateString('pt-PT', options);
            
            eventTitle.textContent = `Nenhum evento em ${formattedDate}`;
            eventContent.innerHTML = '<p class="text-muted">Não há eventos programados para esta data.</p>';
            eventDetails.style.display = 'block';
          }
        },
        height: 'auto',
        fixedWeekCount: false,
        dayMaxEvents: true
      });

      calendar.render();

      // Close event details when clicking outside
      document.addEventListener('click', function(e) {
        if (!eventDetails.contains(e.target) && !calendarEl.contains(e.target)) {
          eventDetails.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>