<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Segurança</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="">
      <div class="mb-3">
        <label for="current_password" class="form-label">Senha Atual</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
      </div>
      <div class="mb-3">
        <label for="new_password" class="form-label">Nova Senha</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
        <div class="form-text">Mínimo 8 caracteres</div>
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" name="change_password" class="btn btn-primary w-100">
        <i class="bi bi-key me-2"></i>Alterar Senha
      </button>
    </form>
  </div>
</div>