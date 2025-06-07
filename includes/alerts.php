<?php
// Exibe mensagens de sucesso/erro armazenadas na sessão ou variáveis locais
if (isset($_SESSION['success_message']) || isset($success_message) || isset($_SESSION['error_message']) || isset($error_message)):
?>
  <div class="alerts-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php unset($_SESSION['success_message']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php unset($_SESSION['error_message']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>