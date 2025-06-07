<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/login.php?erro=5');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Atena - Sua Representação Estudantil</title>
    <?php include 'includes/head.html'; ?>
    <style>
        .hero-section {
            background: linear-gradient(135deg, #001f3f 0%, #003366 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        .feature-card {
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            border-radius: 10px;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .icon-feature {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #001f3f;
        }
        .btn-cta {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 50px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="flex-grow-1">
    <!-- Hero Section -->
    <section class="hero-section text-center">
      <div class="container">
        <h1 class="display-4 fw-bold mb-3">Bem-vindo à Lista Atena</h1>
        <p class="lead mb-4">Sua voz ativa na representação estudantil. Juntos construímos uma escola melhor!</p>
        <div class="d-flex gap-3 justify-content-center">
          <a href="eventos.php" class="btn btn-light btn-cta">Ver Eventos</a>
          <a href="#features" class="btn btn-outline-light btn-cta">Conheça Mais</a>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="container py-5">
      <h2 class="text-center mb-5">O Que Oferecemos</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card feature-card p-4 text-center">
            <div class="icon-feature">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h3>Eventos</h3>
            <p>Palestras, workshops e atividades culturais para complementar sua formação.</p>
            <a href="eventos.php" class="btn btn-outline-primary mt-auto">Ver Agenda</a>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card feature-card p-4 text-center">
            <div class="icon-feature">
              <i class="bi bi-megaphone"></i>
            </div>
            <h3>Representação</h3>
            <p>Levamos suas demandas e sugestões para a direção da escola.</p>
            <a href="contato.php" class="btn btn-outline-primary mt-auto">Fale Conosco</a>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card feature-card p-4 text-center">
            <div class="icon-feature">
              <i class="bi bi-people"></i>
            </div>
            <h3>Comunidade</h3>
            <p>Conecte-se com outros estudantes e participe de projetos conjuntos.</p>
            <a href="sobre.php" class="btn btn-outline-primary mt-auto">Saiba Mais</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="bg-light py-5">
      <div class="container text-center">
        <h2 class="mb-4">Pronto para se envolver?</h2>
        <p class="lead mb-4">Junte-se a nós e faça parte da mudança!</p>
        <a href="participar.php" class="btn btn-primary btn-cta">Quero Participar</a>
      </div>
    </section>

    <!-- Latest News/Events (optional) -->
    <?php
    // Você pode incluir aqui um bloco dinâmico com os próximos eventos
    // usando include ou fazendo uma query diretamente
    // include 'includes/latest_events.php';
    ?>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>