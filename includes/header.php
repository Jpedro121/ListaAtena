
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista Atena</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
<header>
  <nav class="navbar navbar-expand-lg navbar-dark px-3" style="background-color: #001f3f;">
    <a class="navbar-brand" href="/ListaAtena/">Lista Atena Logo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarMenu">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/ListaAtena/">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/ListaAtena/eventos.php">Eventos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/ListaAtena/palestras.php">Palestras</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/ListaAtena/informacoes.php">Informações</a>
        </li>
        <li class="nav-item">
          <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && !empty($_SESSION['nome'])): ?>
            <div class="dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-fill me-2"></i> 
                <span><?= htmlspecialchars($_SESSION['nome']) ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="/ListaAtena/perfil.php"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="/ListaAtena/login/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Terminar Sessão</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a class="nav-link" href="/ListaAtena/login/login.php">
              <i class="bi bi-person-fill"></i> Login
            </a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </nav>
</header>