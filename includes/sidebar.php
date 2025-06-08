<?php
// Verificação de segurança
if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" id="sidebarMenu">
    <div class="position-sticky pt-3 vh-100">
        <!-- Cabeçalho com Logo -->
        <div class="text-center mb-4 px-3">
            <a href="dashboard.php" class="d-flex align-items-center justify-content-center text-white text-decoration-none">
                <i class="bi bi-shield-lock fs-3 me-2"></i>
                <span class="fs-4">Painel Admin</span>
            </a>
            <hr class="mt-3 bg-light">
        </div>

        <!-- Menu Principal -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="../admin/" class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="utilizadores.php" class="nav-link text-white <?= $current_page == 'utilizadores.php' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill me-2"></i>
                    Utilizadores
                </a>
            </li>
            <li class="nav-item">
                <a href="eventos_admin.php" class="nav-link text-white <?= $current_page == 'eventos_admin.php' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event me-2"></i>
                    Eventos
                </a>
            </li>
            <li class="nav-item">
                <a href="configuracoes.php" class="nav-link text-white <?= $current_page == 'configuracoes.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear-fill me-2"></i>
                    Configurações
                </a>
            </li>
        </ul>

        <!-- Menu Secundário (Sair) -->
        <div class="border-top border-secondary px-3 pt-3 mt-auto">
            <a href="../login/logout.php" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right me-2"></i>
                Sair
            </a>
        </div>
    </div>
</div>

<!-- Botão Mobile -->
<button class="navbar-toggler position-fixed d-md-none bg-primary" type="button" 
        data-bs-toggle="collapse" data-bs-target="#sidebarMenu" 
        style="left: 10px; top: 10px; z-index: 1000; padding: 0.35rem 0.75rem;">
    <i class="bi bi-list text-white"></i>
</button>

<style>
    /* Estilos da Sidebar */
    .sidebar {
        background: linear-gradient(180deg,rgb(20, 27, 49) 0%,rgb(20, 27, 49) 100%);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .nav-link {
        border-radius: 0;
        margin: 0.1rem 0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15) !important;
        border-left: 3px solid #fff;
        font-weight: 600;
    }
    
    .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-link.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>