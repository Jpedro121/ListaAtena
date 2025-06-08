<?php
require '../db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger">ID do evento inválido.</div>');
}

$id = (int)$_GET['id'];
$sql = "SELECT e.*, u.nome as organizador 
        FROM eventos e
        LEFT JOIN utilizadores u ON e.id_organizador = u.id
        WHERE e.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('<div class="alert alert-danger">Evento não encontrado.</div>');
}

$evento = $result->fetch_assoc();
?>

<div class="row">
    <?php if (!empty($evento['imagem'])): ?>
    <div class="col-md-4 mb-3">
        <img src="../static/eventos/<?= htmlspecialchars($evento['imagem']) ?>" 
             class="img-fluid rounded" 
             alt="<?= htmlspecialchars($evento['titulo']) ?>">
    </div>
    <?php endif; ?>
    
    <div class="<?= !empty($evento['imagem']) ? 'col-md-8' : 'col-12' ?>">
        <h4><?= htmlspecialchars($evento['titulo']) ?></h4>
        
        <div class="mb-3">
            <span class="badge bg-<?= 
                $evento['tipo'] == 'palestra' ? 'success' : 
                ($evento['tipo'] == 'evento' ? 'danger' : 'warning') ?>">
                <?= ucfirst($evento['tipo']) ?>
            </span>
        </div>
        
        <p><strong><i class="bi bi-calendar-event"></i> Data:</strong> 
           <?= date('d/m/Y', strtotime($evento['data'])) ?></p>
           
        <?php if (!empty($evento['hora'])): ?>
        <p><strong><i class="bi bi-clock"></i> Hora:</strong> 
           <?= date('H:i', strtotime($evento['hora'])) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($evento['local'])): ?>
        <p><strong><i class="bi bi-geo-alt"></i> Local:</strong> 
           <?= htmlspecialchars($evento['local']) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($evento['organizador'])): ?>
        <p><strong><i class="bi bi-person"></i> Organizador:</strong> 
           <?= htmlspecialchars($evento['organizador']) ?></p>
        <?php endif; ?>
        
        <div class="mt-4">
            <h5>Descrição:</h5>
            <p><?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
        </div>
    </div>
</div>