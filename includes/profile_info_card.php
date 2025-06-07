<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Informações Pessoais</h5>
  </div>
  <div class="card-body">
    <ul class="list-group list-group-flush">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-bold">Nome:</span>
        <span><?= htmlspecialchars($user['nome']) ?></span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-bold">Email:</span>
        <span><?= htmlspecialchars($user['email']) ?></span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-bold">Tipo de Conta:</span>
        <span class="badge bg-<?= $user['tipo'] === 'admin' ? 'warning text-dark' : 'primary' ?>">
          <?= ucfirst($user['tipo']) ?>
        </span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-bold">Membro desde:</span>
        <span><?= date('d/m/Y', strtotime($user['data_registo'])) ?></span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-bold">Último login:</span>
        <span><?= $user['ultimo_login'] ? date('d/m/Y H:i', strtotime($user['ultimo_login'])) : 'Nunca' ?></span>
      </li>
    </ul>
  </div>
</div>