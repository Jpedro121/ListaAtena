<?php
require '../includes/db.php';
require '../includes/check_admin.php';

// Basic initialization and security
$_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$total_pages = 1;

// Count total events
$sql_count = "SELECT COUNT(*) as total FROM eventos";
$result_count = $conn->query($sql_count);

if ($result_count && $result_count->num_rows > 0) {
    $total_events = (int)$result_count->fetch_assoc()['total'];
    $total_pages = max(1, ceil($total_events / $limit));
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * $limit;
}

// Get events with pagination
$sql = "SELECT id, titulo, data, hora, descricao, local, imagem, tipo, criado_em 
        FROM eventos 
        ORDER BY data DESC, hora DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Database error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Event Management | Admin Panel</title>
    <?php include '../includes/head.html'; ?>
    <style>
        :root {
            --primary: #001f3f;
            --secondary: #858796;
            --success: #1cc88a;
            --danger: #e74a3b;
            --warning: #f6c23e;
            --info: #36b9cc;
            --dark: #5a5c69;
            --light: #f8f9fc;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: var(--primary);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px 15px;
            margin: 2px 10px;
            border-radius: 4px;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 8px;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .table th {
            background: var(--dark);
            color: white;
            font-weight: 500;
        }
        
        .event-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .badge-event {
            background: var(--primary);
        }
        
        .badge-palestra {
            background: var(--info);
        }
        
        .badge-workshop {
            background: var(--warning);
            color: #212529;
        }
        
        .badge-reuniao {
            background: var(--secondary);
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            margin: 0 2px;
        }
        
        .no-events {
            padding: 40px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="eventos_admin.php">
                                <i class="bi bi-calendar-event"></i> Eventos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="utilizadores.php">
                                <i class="bi bi-people"></i> Utilizadores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../perfil.php">
                                <i class="bi bi-person"></i> Meu Perfil
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-calendar-event me-2"></i> Gestão de Eventos</h2>
                    <a href="adicionar_evento.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Novo Evento
                    </a>
                </div>

                <div class="table-container">
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Imagem</th>
                                        <th>Título</th>
                                        <th>Data/Hora</th>
                                        <th>Local</th>
                                        <th>Tipo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($evento = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php if ($evento['imagem']): ?>
                                                    <img src="../static/eventos/<?= htmlspecialchars($evento['imagem']) ?>" 
                                                         class="event-image" 
                                                         alt="<?= htmlspecialchars($evento['titulo']) ?>">
                                                <?php else: ?>
                                                    <div class="event-image bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($evento['titulo']) ?></td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($evento['data'])) ?>
                                                <?php if ($evento['hora']): ?>
                                                    <br><small><?= date('H:i', strtotime($evento['hora'])) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $evento['local'] ? htmlspecialchars($evento['local']) : '-' ?></td>
                                            <td>
                                                <?php
                                                $badge_class = 'badge-' . strtolower($evento['tipo']);
                                                ?>
                                                <span class="badge rounded-pill <?= $badge_class ?>">
                                                    <?= ucfirst(htmlspecialchars($evento['tipo'])) ?>
                                                </span>
                                            </td>
                                           <td>
                                            <div class="d-flex">
                                                    <a href="editar_evento.php?id=<?= $evento['id'] ?>" 
                                                    class="btn btn-sm btn-outline-secondary action-btn" 
                                                    title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    <a href="/ListaAtena/admin/eliminar_evento.php?id=<?= $evento['id'] ?>" 
                                                    class="btn btn-sm btn-outline-danger action-btn" 
                                                    title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este evento e todas as suas inscrições?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                            </div>   
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-events">
                            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                            <h4 class="mt-3">Nenhum evento encontrado</h4>
                            <p class="text-muted">Comece adicionando um novo evento</p>
                            <a href="adicionar_evento.php" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Adicionar Evento
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple confirmation for delete actions
        document.querySelectorAll('.btn-outline-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja excluir este evento?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>