<?php

/**
 * Trabalho realizado por:
 * Luiz Felippe Luna do Nascimento - RGM: 40338207
 * Nathan Henrique Wysocki RGM39879763
 * Willian cordeiro RGM 40333337
 */
/**
 * Router Principal e Landing Page
 * Redireciona usuários logados e exibe página inicial para visitantes
 */

require_once __DIR__ . '/../config/config.php';

initApp();

// Verificar se há usuário logado e redirecionar
if (isset($_SESSION['cliente_id'])) {
  redirect('cliente/dashboard.php');
}

if (isset($_SESSION['barbeiro_id'])) {
  redirect('barbeiro/dashboard.php');
}

// Buscar alguns barbeiros para exibir na página inicial
$barbeiro = new Barbeiro();
$barbeiros_destaque = $barbeiro->getBarbeirosAtivos(6); // Primeiros 6 barbeiros ativos

include '../views/layouts/header.php';
?>

<!-- Hero Section -->
<div class="hero-section-modern">
  <div class="hero-overlay"></div>
  <div class="container">
    <div class="row min-vh-100 align-items-center">
      <div class="col-lg-6 hero-content">
        <div class="hero-badge fade-in-up">
          <i class="bi bi-scissors me-2"></i>
          Sistema Premium de Agendamento
        </div>
        <h1 class="hero-title-modern fade-in-up">
          Transforme sua <span class="text-gradient-gold">Barbearia</span>
          com Tecnologia de Ponta
        </h1>
        <p class="hero-subtitle-modern fade-in-up">
          Conecte barbeiros e clientes através de uma plataforma moderna e intuitiva.
          Agende, gerencie e construa relacionamentos duradouros.
        </p>
        <div class="hero-buttons fade-in-up">
          <a href="<?= BASE_URL ?>auth/cliente_register.php" class="btn btn-hero-primary">
            <i class="bi bi-person-plus me-2"></i>
            Começar como Cliente
            <i class="bi bi-arrow-right ms-2"></i>
          </a>
          <a href="<?= BASE_URL ?>auth/barbeiro_register.php" class="btn btn-hero-outline">
            <i class="bi bi-scissors me-2"></i>
            Sou Barbeiro
          </a>
        </div>
        <div class="hero-stats fade-in-up">
          <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Agendamentos</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">50+</span>
            <span class="stat-label">Barbeiros</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">100%</span>
            <span class="stat-label">Satisfação</span>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-visual">
          <div class="floating-card card-1 fade-in-up">
            <i class="bi bi-calendar-check"></i>
            <span>Agendamento Fácil</span>
          </div>
          <div class="floating-card card-2 fade-in-up">
            <i class="bi bi-clock"></i>
            <span>Horários Flexíveis</span>
          </div>
          <div class="floating-card card-3 fade-in-up">
            <i class="bi bi-star-fill"></i>
            <span>Qualidade Garantida</span>
          </div>
          <div class="hero-main-icon">
            <i class="bi bi-scissors pulse-gold"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Como Funciona -->
<div class="section-modern">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center mb-5">
        <div class="section-header">
          <span class="section-badge">Processo Simples</span>
          <h2 class="section-title">Como <span class="text-gradient-gold">Funciona</span></h2>
          <p class="section-subtitle">Uma experiência simplificada para todos</p>
        </div>
      </div>
    </div>

    <div class="row g-5">
      <!-- Para Clientes -->
      <div class="col-lg-6">
        <div class="process-section">
          <div class="process-header">
            <div class="process-icon client-icon">
              <i class="bi bi-person-circle"></i>
            </div>
            <h3 class="process-title">Para Clientes</h3>
            <p class="process-subtitle">Agende em minutos</p>
          </div>

          <div class="process-steps">
            <div class="process-step">
              <div class="step-number text-black">01</div>
              <div class="step-content">
                <h5>Cadastre-se Gratuitamente</h5>
                <p>Crie sua conta em segundos com informações básicas</p>
              </div>
            </div>

            <div class="process-step">
              <div class="step-number text-black">02</div>
              <div class="step-content">
                <h5>Escolha seu Barbeiro</h5>
                <p>Navegue por perfis detalhados e especialidades</p>
              </div>
            </div>

            <div class="process-step">
              <div class="step-number text-black">03</div>
              <div class="step-content">
                <h5>Selecione o Serviço</h5>
                <p>Corte, barba, tratamentos e muito mais</p>
              </div>
            </div>

            <div class="process-step">
              <div class="step-number text-black">04</div>
              <div class="step-content">
                <h5>Confirme o Agendamento</h5>
                <p>Escolha data, horário e pronto!</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Para Barbeiros -->
      <div class="col-lg-6">
        <div class="process-section">
          <div class="process-header">
            <div class="process-icon barber-icon">
              <i class="bi bi-scissors"></i>
            </div>
            <h3 class="process-title">Para Barbeiros</h3>
            <p class="process-subtitle">Gerencie seu negócio</p>
          </div>

          <div class="process-steps">
            <div class="process-step">
              <div class="step-number text-black">01</div>
              <div class="step-content">
                <h5>Crie seu Perfil</h5>
                <p>Mostre suas habilidades e experiência</p>
              </div>
            </div>

            <div class="process-step">
              <div class="step-number text-black">02</div>
              <div class="step-content">
                <h5>Configure Serviços</h5>
                <p>Defina preços, durações e especialidades</p>
              </div>
            </div>

            <div class="process-step">
              <div class="step-number text-black">03</div>
              <div class="step-content">
                <h5>Organize sua Agenda</h5>
                <p>Configure horários e disponibilidade</p>
              </div>
            </div>

            <div class="process-step">
              <div class="step-number text-black">04</div>
              <div class="step-content">
                <h5>Receba Clientes</h5>
                <p>Acompanhe agendamentos em tempo real</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Barbeiros em Destaque -->
<?php if (!empty($barbeiros_destaque)): ?>
  <div class="featured-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center mb-5">
          <div class="section-header">
            <span class="section-badge">Nossos Profissionais</span>
            <h2 class="section-title">Barbeiros em <span class="text-gradient-gold">Destaque</span></h2>
            <p class="section-subtitle">Conheça alguns de nossos especialistas</p>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <?php foreach ($barbeiros_destaque as $barb): ?>
          <div class="col-md-6 col-lg-4">
            <div class="barber-card fade-in-up">
              <div class="barber-avatar">
                <div class="avatar-placeholder">
                  <i class="bi bi-person"></i>
                </div>
                <div class="barber-badge">
                  <i class="bi bi-star-fill"></i>
                </div>
              </div>
              <div class="barber-info">
                <h5 class="barber-name"><?= htmlspecialchars($barb['nome']) ?></h5>
                <?php if (!empty($barb['especialidade'])): ?>
                  <p class="barber-specialty"><?= htmlspecialchars($barb['especialidade']) ?></p>
                <?php endif; ?>
                <div class="barber-stats">
                  <div class="stat">
                    <i class="bi bi-calendar-check"></i>
                    <span>200+ atendimentos</span>
                  </div>
                  <div class="stat">
                    <i class="bi bi-star-fill"></i>
                    <span>4.9/5 avaliação</span>
                  </div>
                </div>
                <a href="<?= BASE_URL ?>auth/cliente_register.php" class="btn btn-barber-card text-light-gray">
                  <i class="bi bi-calendar-plus me-2"></i>
                  Agendar Horário
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="text-center mt-5">
        <a href="<?= BASE_URL ?>auth/cliente_register.php" class="btn btn-featured-main text-light-gray">
          <i class="bi bi-person-plus me-2"></i>
          Ver Todos os Profissionais
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Benefícios -->
<div class="benefits-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center mb-5">
        <div class="section-header">
          <span class="section-badge">Vantagens Exclusivas</span>
          <h2 class="section-title">Por que Escolher <span class="text-gradient-gold">Nosso Sistema</span>?</h2>
          <p class="section-subtitle">Tecnologia que faz a diferença</p>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="benefit-card fade-in-up">
          <div class="benefit-icon time-icon">
            <i class="bi bi-clock"></i>
          </div>
          <h5 class="benefit-title">Economia de Tempo</h5>
          <p class="benefit-description">Agende em segundos pelo celular ou computador, sem filas ou espera</p>
          <div class="benefit-highlight">
            <span>90% mais rápido</span>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="benefit-card fade-in-up">
          <div class="benefit-icon security-icon">
            <i class="bi bi-shield-check"></i>
          </div>
          <h5 class="benefit-title">Máxima Segurança</h5>
          <p class="benefit-description">Seus dados protegidos com criptografia de última geração</p>
          <div class="benefit-highlight">
            <span>100% seguro</span>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="benefit-card fade-in-up">
          <div class="benefit-icon notification-icon">
            <i class="bi bi-bell"></i>
          </div>
          <h5 class="benefit-title">Lembretes Inteligentes</h5>
          <p class="benefit-description">Notificações automáticas para você nunca perder um agendamento</p>
          <div class="benefit-highlight">
            <span>0 esquecimentos</span>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="benefit-card fade-in-up">
          <div class="benefit-icon quality-icon">
            <i class="bi bi-star"></i>
          </div>
          <h5 class="benefit-title">Qualidade Premium</h5>
          <p class="benefit-description">Apenas barbeiros verificados e com excelente reputação</p>
          <div class="benefit-highlight">
            <span>5⭐ garantido</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Features adiccionais -->
    <div class="row mt-5">
      <div class="col-lg-8 mx-auto">
        <div class="features-grid">
          <div class="feature-item">
            <i class="bi bi-phone"></i>
            <span>App Mobile</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-calendar-week"></i>
            <span>Agenda Flexível</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-credit-card"></i>
            <span>Pagamento Online</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-chat-dots"></i>
            <span>Suporte 24/7</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-geo-alt"></i>
            <span>Localização GPS</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-graph-up"></i>
            <span>Relatórios</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Call to Action -->
<div class="cta-section">
  <div class="cta-overlay"></div>
  <div class="container">
    <div class="row">
      <div class="col-lg-8 mx-auto text-center">
        <div class="cta-content">
          <div class="cta-badge fade-in-up">
            <i class="bi bi-rocket-takeoff me-2"></i>
            Comece Agora
          </div>
          <h2 class="cta-title fade-in-up">
            Pronto para <span class="text-gradient-gold">Transformar</span>
            sua Experiência?
          </h2>
          <p class="cta-subtitle fade-in-up">
            Junte-se a milhares de clientes e barbeiros que já descobriram
            uma nova forma de agendar e gerenciar serviços de barbearia.
          </p>

          <div class="cta-buttons fade-in-up">
            <a href="<?= BASE_URL ?>auth/cliente_register.php" class="btn btn-cta-primary">
              <i class="bi bi-person-plus me-2"></i>
              Começar como Cliente
              <i class="bi bi-arrow-right ms-2"></i>
            </a>
            <a href="<?= BASE_URL ?>auth/barbeiro_register.php" class="btn btn-cta-outline">
              <i class="bi bi-scissors me-2"></i>
              Cadastrar Barbearia
            </a>
          </div>

          <div class="cta-trust fade-in-up">
            <div class="trust-item">
              <i class="bi bi-shield-check"></i>
              <span>Gratuito para começar</span>
            </div>
            <div class="trust-item">
              <i class="bi bi-clock"></i>
              <span>Configuração em 2 minutos</span>
            </div>
            <div class="trust-item">
              <i class="bi bi-headset"></i>
              <span>Suporte especializado</span>
            </div>
          </div>

          <hr class="cta-divider">

          <div class="login-links">
            <p class="login-text">Já possui uma conta?</p>
            <div class="login-buttons">
              <a href="<?= BASE_URL ?>auth/cliente_login.php" class="btn-link-gold">
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Login Cliente
              </a>
              <span class="divider">|</span>
              <a href="<?= BASE_URL ?>auth/barbeiro_login.php" class="btn-link-gold">
                <i class="bi bi-scissors me-1"></i>
                Login Barbeiro
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../views/layouts/footer.php'; ?>

<style>
  /* Adicione no CSS (public/assets/css/style.css ou text-contrast.css): */
  .text-light-gray {
    color: #f1f1f1 !important;
  }
</style>