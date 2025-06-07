<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="avatarModalLabel">Alterar Foto de Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="includes/upload_avatar.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label for="avatar" class="form-label">Selecione uma imagem</label>
            <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
            <div class="form-text">Formatos suportados: JPG, PNG, GIF. Tamanho máximo: 2MB.</div>
          </div>
          <div class="text-center">
            <img id="avatarPreview" src="<?= !empty($user['foto_perfil']) ? 'uploads/profiles/'.htmlspecialchars($user['foto_perfil']) : 'assets/default-profile.png' ?>" 
                 class="img-thumbnail" 
                 style="max-width: 200px;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>