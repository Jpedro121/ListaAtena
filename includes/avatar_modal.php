<!-- Modal para upload de foto de perfil -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alterar Foto de Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="avatarForm" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="avatarUpload" class="form-label">Selecione uma imagem</label>
            <input class="form-control" type="file" id="avatarUpload" name="avatar" accept="image/*" required>
            <div class="form-text">Formatos: JPG, PNG (Máx. 2MB)</div>
          </div>
          <div class="text-center">
            <img id="avatarPreview" 
                 src="<?= !empty($user['foto_perfil']) ? 'uploads/profiles/'.htmlspecialchars($user['foto_perfil']) : 'assets/default-profile.png' ?>" 
                 class="img-thumbnail mb-3 rounded-circle" 
                 style="width: 150px; height: 150px; object-fit: cover;">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="saveAvatarBtn">Salvar</button>
      </div>
    </div>
  </div>
</div>