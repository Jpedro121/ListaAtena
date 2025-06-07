<?php
require '../includes/db.php';
require '../includes/check_admin.php';

// Processar formulário apenas se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos obrigatórios
    $campos_obrigatorios = ['titulo', 'data', 'descricao', 'tipo'];
    $dados = [];
    $erros = [];
    
    foreach ($campos_obrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            $erros[] = "O campo $campo é obrigatório.";
        } else {
            $dados[$campo] = trim($_POST[$campo]);
        }
    }
    
    // Processar campos opcionais
    $dados['hora'] = !empty($_POST['hora']) ? $_POST['hora'] : null;
    $dados['local'] = !empty($_POST['local']) ? $_POST['local'] : null;
    
    // Processar upload de imagem
    $imagem = null;
    if (!empty($_FILES['imagem']['name'])) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = uniqid() . '.' . $ext;
        $target_dir = "../static/eventos/";
        
        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $target_dir . $imagem)) {
            $erros[] = "Erro ao fazer upload da imagem.";
        }
    }
    
    // Se não houver erros, inserir no banco
    if (empty($erros)) {
        try {
            $stmt = $conn->prepare("INSERT INTO eventos 
                                  (titulo, data, hora, local, descricao, tipo, imagem) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("sssssss", 
                $dados['titulo'],
                $dados['data'],
                $dados['hora'],
                $dados['local'],
                $dados['descricao'],
                $dados['tipo'],
                $imagem
            );
            
            if ($stmt->execute()) {
                header('Location: eventos_admin.php?success=1');
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            $erros[] = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}

// Obter eventos existentes
$sql = "SELECT * FROM eventos ORDER BY data DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Admin - Eventos</title>
  <?php include '../includes/head.html'; ?>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <main class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Gestão de Eventos</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarEventoModal">
        Adicionar Evento
      </button>
    </div>

    <?php if (!empty($erros)): ?>
      <div class="alert alert-danger">
        <?php foreach ($erros as $erro): ?>
          <p><?= htmlspecialchars($erro) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">Evento adicionado com sucesso!</div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-striped">
        <!-- Tabela de eventos existente -->
      </table>
    </div>
  </main>

  <!-- Modal para adicionar evento -->
  <div class="modal fade" id="adicionarEventoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Adicionar Novo Evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Título*</label>
              <input type="text" class="form-control" name="titulo" required>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Data*</label>
                <input type="date" class="form-control" name="data" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Hora</label>
                <input type="time" class="form-control" name="hora">
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Local</label>
              <input type="text" class="form-control" name="local">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Tipo*</label>
              <select class="form-select" name="tipo" required>
                <option value="evento">Evento</option>
                <option value="palestra">Palestra</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Imagem</label>
              <input type="file" class="form-control" name="imagem" accept="image/*">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Descrição*</label>
              <textarea class="form-control" name="descricao" rows="4" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>
</html>   ADICIONAR EVENTOS BOMA NAO FUNCIONA 