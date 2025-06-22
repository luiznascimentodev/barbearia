</div>
</main>

<!-- Footer Compacto -->
<footer class="footer py-4">
  <div class="footer-curve"></div>
  <div class="container position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <a href="<?= BASE_URL ?>" class="footer-brand">
          <i class="bi bi-scissors me-2"></i> Barbearia <span class="text-gradient-gold">Pro</span>
        </a>
        <span class="ms-3 small text-muted">© <?= date('Y') ?> Todos os direitos reservados</span>
      </div>
      <div class="social-icons">
        <a href="#" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" class="social-icon" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        <a href="#" class="social-icon" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </div>
</footer>

<style>
  /* Estilos do footer moderno */
  .footer {
    position: relative;
    background: rgba(10, 10, 10, 0.7);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    z-index: 10;
  }

  .footer-curve {
    position: absolute;
    top: -50px;
    left: 0;
    width: 100%;
    height: 50px;
    background: rgba(10, 10, 10, 0.7);
    backdrop-filter: blur(10px);
    clip-path: ellipse(55% 100% at 50% 100%);
    z-index: -1;
  }

  .footer-brand {
    font-weight: 700;
    font-size: 1.3rem;
    color: white !important;
    text-decoration: none;
    transition: var(--transition);
  }

  .text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
  }

  .social-icons {
    display: flex;
    gap: 0.8rem;
  }

  .social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    transition: var(--transition);
    text-decoration: none;
    position: relative;
    overflow: hidden;
  }

  .social-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--gold-darker) 0%, var(--gold) 100%);
    opacity: 0;
    transition: var(--transition);
    z-index: -1;
    transform: scale(0.5);
    border-radius: 50%;
  }

  .social-icon:hover {
    color: var(--dark-color);
    transform: translateY(-3px);
  }

  .social-icon:hover::before {
    opacity: 1;
    transform: scale(1);
  }

  @media (max-width: 576px) {
    .footer .d-flex {
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }

    .social-icons {
      justify-content: center;
    }
  }
</style>

<!-- Bootstrap JS (necessário para modais e componentes interativos) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts personalizados -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Ativar links do menu atual
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
      }
    });

    // Controlar o comportamento da navbar no scroll
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // Efeito de aparecimento em cascata para listas
    const staggerItems = document.querySelectorAll('.stagger-item');
    if (staggerItems.length) {
      staggerItems.forEach((item, index) => {
        setTimeout(() => {
          item.classList.add('animate');
        }, 100 * index);
      });
    }

    // Aplicar efeito de loading em botões de formulário
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.classList.add('btn-loading', 'active');
          submitBtn.setAttribute('disabled', true);
        }
      });
    });

    // Adicionar ripple effect nos botões
    document.querySelectorAll('.btn').forEach(button => {
      button.classList.add('btn-ripple');

      button.addEventListener('click', function(e) {
        const rect = button.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement('span');
        ripple.style.position = 'absolute';
        ripple.style.width = '1px';
        ripple.style.height = '1px';
        ripple.style.borderRadius = '50%';
        ripple.style.transform = 'scale(0)';
        ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.4)';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.animation = 'ripple-effect 0.6s linear';

        button.appendChild(ripple);

        setTimeout(() => {
          ripple.remove();
        }, 600);
      });
    });
  });

  // Criar efeito de partículas no fundo
  function createParticles() {
    const hero = document.querySelector('.hero-section-modern');
    if (hero) {
      hero.classList.add('particles-bg');

      // Criar 20 partículas
      for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';

        // Posição aleatória
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;

        particle.style.top = `${posY}%`;
        particle.style.left = `${posX}%`;
        particle.style.opacity = Math.random() * 0.5 + 0.1;

        // Tamanho aleatório
        const size = Math.random() * 5 + 2;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;

        // Atraso de animação aleatório
        particle.style.animationDelay = `${Math.random() * 5}s`;

        hero.appendChild(particle);
      }
    }
  }

  createParticles();

  // Forçar estilos escuros em elementos de tabela
  function fixTableStyles() {
    // Corrigir cabeçalhos de tabela claros
    document.querySelectorAll('.table-light, .table > .table-light, thead.table-light')
      .forEach(item => {
        if (!item.classList.contains('dark-fixed')) {
          item.style.backgroundColor = 'rgba(33, 37, 41, 0.8)';
          item.style.color = 'rgba(255, 255, 255, 0.9)';
          item.classList.add('dark-fixed');
        }
      });

    // Corrigir campos de formulário com fundo claro
    document.querySelectorAll('input[type="date"], input[type="time"], input[type="datetime-local"]')
      .forEach(item => {
        if (!item.classList.contains('dark-fixed')) {
          item.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
          item.style.color = 'rgba(255, 255, 255, 0.9)';
          item.style.borderColor = 'rgba(255, 255, 255, 0.1)';
          item.classList.add('dark-fixed');
        }
      });
  }

  // Executar imediatamente e sempre que o DOM mudar
  fixTableStyles();

  // Usar um MutationObserver para detectar mudanças no DOM
  const observer = new MutationObserver(mutations => {
    fixTableStyles();
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true
  });

  // Detecção de scroll para animações
  window.addEventListener('scroll', function() {
    const animateOnScroll = document.querySelectorAll('.animate-on-scroll');

    animateOnScroll.forEach(element => {
      const elementTop = element.getBoundingClientRect().top;
      const windowHeight = window.innerHeight;

      if (elementTop < windowHeight * 0.9) {
        element.classList.add('animated');
      }
    });
  });
</script>

<style>
  @keyframes ripple-effect {
    to {
      transform: scale(30);
      opacity: 0;
    }
  }

  .animate-on-scroll {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
  }

  .animate-on-scroll.animated {
    opacity: 1;
    transform: translateY(0);
  }
</style>
<noscript>
  <style>
    .dropdown:hover .dropdown-menu {
      display: block;
    }

    .animate-on-scroll,
    .stagger-item {
      opacity: 1;
      transform: none;
    }
  </style>
</noscript>
</body>

</html>