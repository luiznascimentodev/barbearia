<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title . ' - ' : '' ?>Barbearia - Sistema de Agendamento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/style.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/animations.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/transitions.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/forms.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/dark-fixes.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/text-contrast.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/table-styles.css" rel="stylesheet">
  <link href="<?= defined('BASE_URL') ? BASE_URL : '/barbearia/public/' ?>assets/css/dark-buttons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Body com fundo escuro gradiente para combinar com as seções modernas */
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #000000 100%);
      color: #fff;
      position: relative;
      overflow-x: hidden;
      padding-top: 76px;
      /* Espaço para o navbar fixo */
    }

    /* Navbar aprimorado com efeito de glass e fixed-top */
    .navbar {
      padding: 1rem 0;
      background: rgba(10, 10, 10, 0.85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      transition: all 0.3s ease;
    }

    .navbar.scrolled {
      padding: 0.5rem 0;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    /* Estilizando o toggle button do menu */
    .custom-navbar-toggler {
      border: none;
      background: transparent;
      padding: 0;
      width: 30px;
      height: 24px;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .toggler-icon {
      display: block;
      width: 100%;
      height: 2px;
      background-color: rgba(255, 255, 255, 0.8);
      transition: all 0.3s ease;
      transform-origin: left center;
    }

    .custom-navbar-toggler:focus {
      box-shadow: none;
    }

    .navbar-toggler[aria-expanded="true"] .toggler-icon:first-child {
      transform: rotate(45deg);
    }

    .navbar-toggler[aria-expanded="true"] .toggler-icon:nth-child(2) {
      opacity: 0;
    }

    .navbar-toggler[aria-expanded="true"] .toggler-icon:last-child {
      transform: rotate(-45deg);
    }

    /* Adiciona elementos de fundo sutis como o index.php */
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 30% 20%,
          rgba(255, 193, 7, 0.03) 0%,
          transparent 50%),
        radial-gradient(circle at 70% 80%,
          rgba(255, 193, 7, 0.02) 0%,
          transparent 50%);
      pointer-events: none;
      z-index: 0;
    }

    /* Conteúdo principal ocupando todo o espaço vertical disponível */
    .main-content {
      flex: 1;
      position: relative;
      z-index: 1;
    }

    /* Ajustes para elementos internos */
    .alert {
      margin-bottom: 0;
    }

    .card-hover:hover {
      transform: translateY(-2px);
      transition: transform 0.2s;
    }

    .status-badge {
      font-size: 0.8em;
    }

    .table-responsive {
      border-radius: 0.375rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Breadcrumb com fundo escuro */
    .breadcrumb {
      background: rgba(255, 255, 255, 0.05);
      padding: 0.75rem 1rem;
      border-radius: var(--border-radius);
    }

    .breadcrumb-item,
    .breadcrumb-item.active {
      color: rgba(255, 255, 255, 0.7);
    }

    .breadcrumb-item a {
      color: var(--accent-color);
      text-decoration: none;
    }

    .breadcrumb-item a:hover {
      color: #ffca28;
    }

    /* Ajuste para dropdown-menu em fundo escuro com efeito de vidro */
    .dropdown-menu {
      background: rgba(33, 37, 41, 0.85);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.8rem;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      margin-top: 0.5rem;
      padding: 0.75rem 0;
    }

    .dropdown-item {
      color: rgba(255, 255, 255, 0.8);
      padding: 0.6rem 1.5rem;
      transition: all 0.3s ease;
    }

    .dropdown-item:hover {
      background: rgba(255, 193, 7, 0.1);
      color: var(--accent-color);
      transform: translateX(5px);
    }

    .dropdown-header {
      color: var(--accent-color);
    }

    .dropdown-divider {
      border-color: rgba(255, 255, 255, 0.1);
    }
  </style>
</head>

<body>
  <!-- Navbar moderna com estilo consistente -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="<?= BASE_URL ?>">
        <i class="bi bi-scissors me-2"></i> Barbearia <span class="text-gradient-gold">Pro</span>
      </a>

      <button class="navbar-toggler custom-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="toggler-icon"></span>
        <span class="toggler-icon"></span>
        <span class="toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>"><i class="bi bi-house-door me-1"></i> Início</a>
            </li>
          <?php endif; ?>

          <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'cliente'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>cliente/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>cliente/novo_agendamento.php"><i class="bi bi-calendar-plus me-1"></i> Novo Agendamento</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>cliente/agendamentos.php"><i class="bi bi-calendar-check me-1"></i> Meus Agendamentos</a>
            </li>
          <?php endif; ?>

          <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'barbeiro'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>barbeiro/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>barbeiro/agendamentos.php"><i class="bi bi-calendar me-1"></i> Agendamentos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>barbeiro/servicos.php"><i class="bi bi-scissors me-1"></i> Serviços</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>barbeiro/horarios.php"><i class="bi bi-clock me-1"></i> Horários</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>barbeiro/clientes.php"><i class="bi bi-people me-1"></i> Clientes</a>
            </li>
          <?php endif; ?>
        </ul>

        <ul class="navbar-nav">
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> <?= $_SESSION['user_name'] ?>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <?php if ($_SESSION['user_type'] !== 'barbeiro'): ?>
                    <a class="dropdown-item" href="<?= BASE_URL ?><?= $_SESSION['user_type'] ?>/perfil.php">
                      <i class="bi bi-person"></i> Meu Perfil
                    </a>
                  <?php endif; ?>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <a class="dropdown-item" href="<?= BASE_URL ?>auth/logout.php">
                    <i class="bi bi-box-arrow-right"></i> Sair
                  </a>
                </li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person"></i> Entrar
              </a>
              <ul class="dropdown-menu">
                <li>
                  <h6 class="dropdown-header">Cliente</h6>
                </li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/cliente_login.php">Login</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/cliente_register.php">Cadastrar</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <h6 class="dropdown-header">Barbeiro</h6>
                </li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/barbeiro_login.php">Login</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/barbeiro_register.php">Cadastrar</a></li>
              </ul>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Mensagens de alerta com estilo moderno -->
  <div class="container py-2">
    <?php include __DIR__ . '/../partials/messages.php'; ?>
  </div>

  <!-- Conteúdo principal -->
  <main class="main-content">
    <div class="container mt-4">
      <?php if (isset($breadcrumb)): ?>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <?php foreach ($breadcrumb as $item): ?>
              <?php if (isset($item['url'])): ?>
                <li class="breadcrumb-item">
                  <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                </li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?= $item['title'] ?></li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ol>
        </nav>
      <?php endif; ?>