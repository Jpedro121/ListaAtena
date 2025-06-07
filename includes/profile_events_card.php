<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Meus Eventos</h5>
  </div>
  <div class="card-body">
    <?php if (empty($events)): ?>
      <div class="text-center py-4">
        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
        <p class="mt-3">Você ainda não se inscreveu em nenhum evento.</p>
        <a href="eventos.php" class="btn btn-primary">Explorar Eventos</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Evento</th>
              <th>Data</th>
              <th>Local</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($events as $event): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <img src="<?= !empty($event['imagem']) ? 'uploads/events/'.htmlspecialchars($event['imagem']) : 'assets/default-event.png' ?>" 
                         alt="<?= htmlspecialchars($event['titulo']) ?>" 
                         class="rounded me-3" 
                         width="60">
                    <div><?= htmlspecialchars($event['titulo']) ?></div>
                  </div>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($event['data'].' '.$event['hora'])) ?></td>
                <td><?= htmlspecialchars($event['local']) ?></td>
                <td>
                  <a href="evento.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>