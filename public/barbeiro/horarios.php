<?php
require_once __DIR__ . '/../../config/config.php';

initApp();

checkBarberAuth();

$barbeiro_id = $_SESSION['barbeiro_id'];

// Processar formulário
if ($_POST) {
  $acao = $_POST['acao'] ?? '';
  $horario = new HorarioDisponivel();

  if ($acao === 'criar_semana') {
    // Criar horários para uma semana completa
    $data_inicio = $_POST['data_inicio'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    $intervalo = (int)($_POST['intervalo'] ?? 30);
    $dias_semana = $_POST['dias_semana'] ?? [];

    $erros = [];

    if (empty($data_inicio)) {
      $erros[] = 'Data de início é obrigatória.';
    }

    if (empty($horario_inicio) || empty($horario_fim)) {
      $erros[] = 'Horários de início e fim são obrigatórios.';
    }

    if ($horario_inicio >= $horario_fim) {
      $erros[] = 'Horário de início deve ser anterior ao horário de fim.';
    }

    if (empty($dias_semana)) {
      $erros[] = 'Selecione pelo menos um dia da semana.';
    }

    if (empty($erros)) {
      try {
        $resultado = $horario->criarHorariosSemana($barbeiro_id, $data_inicio, $horario_inicio, $horario_fim, $intervalo, $dias_semana);

        if ($resultado) {
          setFlashMessage('Horários criados com sucesso!', 'success');
        } else {
          setFlashMessage('Erro ao criar horários.', 'error');
        }
      } catch (Exception $e) {
        error_log("Erro ao criar horários: " . $e->getMessage());
        setFlashMessage('Erro interno. Tente novamente.', 'error');
      }
    } else {
      foreach ($erros as $erro) {
        setFlashMessage($erro, 'error');
      }
    }
  } elseif ($acao === 'criar_dia') {
    // Criar horários para um dia específico
    $data = $_POST['data'] ?? '';
    $horario_inicio = $_POST['horario_inicio_dia'] ?? '';
    $horario_fim = $_POST['horario_fim_dia'] ?? '';
    $intervalo = (int)($_POST['intervalo_dia'] ?? 30);

    $erros = [];

    if (empty($data)) {
      $erros[] = 'Data é obrigatória.';
    }

    if (empty($horario_inicio) || empty($horario_fim)) {
      $erros[] = 'Horários de início e fim são obrigatórios.';
    }

    if ($horario_inicio >= $horario_fim) {
      $erros[] = 'Horário de início deve ser anterior ao horário de fim.';
    }

    if (empty($erros)) {
      try {
        $resultado = $horario->criarHorariosDia($barbeiro_id, $data, $horario_inicio, $horario_fim, $intervalo);

        if ($resultado) {
          setFlashMessage('Horários criados com sucesso!', 'success');
        } else {
          setFlashMessage('Erro ao criar horários.', 'error');
        }
      } catch (Exception $e) {
        error_log("Erro ao criar horários: " . $e->getMessage());
        setFlashMessage('Erro interno. Tente novamente.', 'error');
      }
    } else {
      foreach ($erros as $erro) {
        setFlashMessage($erro, 'error');
      }
    }
  } elseif ($acao === 'remover_data') {
    $data = $_POST['data'] ?? '';

    if ($data) {
      try {
        $resultado = $horario->removerHorariosByData($barbeiro_id, $data);
        setFlashMessage('Horários removidos com sucesso!', 'success');
      } catch (Exception $e) {
        error_log("Erro ao remover horários: " . $e->getMessage());
        setFlashMessage('Erro interno. Tente novamente.', 'error');
      }
    }
  }

  redirect('/barbeiro/horarios.php');
}

// Buscar horários próximos (próximos 14 dias)
$horario = new HorarioDisponivel();
$data_inicio = date('Y-m-d');
$data_fim = date('Y-m-d', strtotime('+14 days'));
$horarios_proximos = $horario->findWithAgendamentoStatus($barbeiro_id);

// Agrupar por data
$horarios_por_data = [];
foreach ($horarios_proximos as $h) {
  $data = date('Y-m-d', strtotime($h['data_hora']));
  $horarios_por_data[$data][] = $h;
}

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/partials/components.php';
?>

<div class="container mt-4">
  <!-- Cabeçalho -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0">Meus Horários</h1>
      <p class="text-muted">Gerencie sua agenda de trabalho</p>
    </div>
    <a href="<?= BASE_URL ?>barbeiro/dashboard.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>
      Dashboard
    </a>
  </div>

  <?php include '../../views/partials/messages.php'; ?>

  <!-- Botões de Ação -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body text-center">
          <i class="bi bi-calendar-week fs-1 text-primary mb-3"></i>
          <h5>Criar Agenda Semanal</h5>
          <p class="text-muted">Defina horários para vários dias da semana</p>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCriarSemana">
            Configurar Semana
          </button>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body text-center">
          <i class="bi bi-calendar-day fs-1 text-success mb-3"></i>
          <h5>Criar Agenda Específica</h5>
          <p class="text-muted">Defina horários para um dia específico</p>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCriarDia">
            Configurar Dia
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Horários Próximos -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bi bi-clock me-2"></i>
        Próximos 14 Dias
      </h5>
    </div>
    <div class="card-body">
      <?php if (empty($horarios_por_data)): ?>
        <?= getEmptyState('Nenhum horário configurado', 'Configure seus horários de trabalho para receber agendamentos.') ?>
      <?php else: ?>
        <div class="row">
          <?php foreach ($horarios_por_data as $data => $horarios): ?>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <strong><?= formatDate($data) ?></strong>
                  <form method="POST" class="d-inline" onsubmit="return confirm('Remover todos os horários desta data?')">
                    <input type="hidden" name="acao" value="remover_data">
                    <input type="hidden" name="data" value="<?= $data ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover todos horários">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
                <div class="card-body">
                  <div class="d-flex flex-wrap gap-2 justify-content-start align-items-center horarios-grid-responsive">
                    <?php foreach ($horarios as $h): ?>
                      <?php if ($h['agendamento_id']): ?>
                        <span class="badge bg-secondary text-wrap px-3 py-2 horario-agendado" title="Agendado para <?= htmlspecialchars($h['cliente_nome']) ?> (<?= htmlspecialchars($h['servico_nome']) ?>)">
                          <?= date('H:i', strtotime($h['data_hora'])) ?><br>
                          <small><?= htmlspecialchars($h['cliente_nome']) ?></small>
                        </span>
                      <?php elseif ($h['disponivel']): ?>
                        <span class="badge bg-success text-wrap px-3 py-2 horario-disponivel">
                          <?= date('H:i', strtotime($h['data_hora'])) ?><br>
                          <small>Disponível</small>
                        </span>
                      <?php else: ?>
                        <span class="badge bg-light text-dark text-wrap px-3 py-2 horario-indisponivel">
                          <?= date('H:i', strtotime($h['data_hora'])) ?><br>
                          <small>Indisponível</small>
                        </span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                  <small class="text-muted d-block mt-2">
                    Total: <?= count($horarios) ?> horários |
                    Livres: <?= count(array_filter($horarios, fn($h) => $h['disponivel'] && !$h['agendamento_id'])) ?>
                  </small>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Criar Semana -->
<div class="modal fade" id="modalCriarSemana" tabindex="-1" aria-labelledby="modalCriarSemanaLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary">
      <form method="POST" id="formCriarSemana">
        <div class="modal-header bg-dark text-white border-bottom border-secondary">
          <h5 class="modal-title text-white" id="modalCriarSemanaLabel">
            <i class="bi bi-calendar-week me-2 text-primary"></i>
            Criar Agenda Semanal
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body bg-dark text-white">
          <input type="hidden" name="acao" value="criar_semana">

          <div class="mb-3">
            <label for="data_inicio" class="form-label text-white">Data de Início</label>
            <input type="date" class="form-control bg-dark text-white border-secondary modal-input-interactive"
              id="data_inicio" name="data_inicio" required autocomplete="off"
              value="<?= date('Y-m-d') ?>"
              style="pointer-events: auto !important; user-select: auto !important;">
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="horario_inicio" class="form-label text-white">Horário de Início</label>
              <input type="time" class="form-control bg-dark text-white border-secondary modal-input-interactive"
                id="horario_inicio" name="horario_inicio" required autocomplete="off"
                value="08:00"
                style="pointer-events: auto !important; user-select: auto !important;">
            </div>
            <div class="col-md-6">
              <label for="horario_fim" class="form-label text-white">Horário de Fim</label>
              <input type="time" class="form-control bg-dark text-white border-secondary modal-input-interactive"
                id="horario_fim" name="horario_fim" required autocomplete="off"
                value="18:00"
                style="pointer-events: auto !important; user-select: auto !important;">
            </div>
          </div>

          <div class="mb-3">
            <label for="intervalo" class="form-label text-white">Intervalo entre horários (minutos)</label>
            <select class="form-select bg-dark text-white border-secondary modal-input-interactive"
              id="intervalo" name="intervalo" autocomplete="off"
              style="pointer-events: auto !important; user-select: auto !important;">
              <option value="15">15 minutos</option>
              <option value="30" selected>30 minutos</option>
              <option value="45">45 minutos</option>
              <option value="60">60 minutos</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label text-white">Dias da Semana</label>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="1" id="seg"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="seg">Segunda-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="2" id="ter"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="ter">Terça-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="3" id="qua"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="qua">Quarta-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="4" id="qui"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="qui">Quinta-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="5" id="sex"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="sex">Sexta-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="6" id="sab"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="sab">Sábado</label>
            </div>
            <div class="form-check">
              <input class="form-check-input modal-input-interactive" type="checkbox" name="dias_semana[]" value="0" id="dom"
                style="pointer-events: auto !important; user-select: auto !important;">
              <label class="form-check-label text-white" for="dom">Domingo</label>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-dark text-white border-top border-secondary">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i>
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>
            Criar Horários
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Criar Dia -->
<div class="modal fade" id="modalCriarDia" tabindex="-1" aria-labelledby="modalCriarDiaLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary">
      <form method="POST" id="formCriarDia">
        <div class="modal-header bg-dark text-white border-bottom border-secondary">
          <h5 class="modal-title text-white" id="modalCriarDiaLabel">
            <i class="bi bi-calendar-day me-2 text-success"></i>
            Criar Agenda Específica
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body bg-dark text-white">
          <input type="hidden" name="acao" value="criar_dia">

          <div class="mb-3">
            <label for="data" class="form-label text-white">Data</label>
            <input type="date" class="form-control bg-dark text-white border-secondary modal-input-interactive"
              id="data" name="data" required autocomplete="off"
              value="<?= date('Y-m-d') ?>"
              style="pointer-events: auto !important; user-select: auto !important;">
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="horario_inicio_dia" class="form-label text-white">Horário de Início</label>
              <input type="time" class="form-control bg-dark text-white border-secondary modal-input-interactive"
                id="horario_inicio_dia" name="horario_inicio_dia" required autocomplete="off"
                value="08:00"
                style="pointer-events: auto !important; user-select: auto !important;">
            </div>
            <div class="col-md-6">
              <label for="horario_fim_dia" class="form-label text-white">Horário de Fim</label>
              <input type="time" class="form-control bg-dark text-white border-secondary modal-input-interactive"
                id="horario_fim_dia" name="horario_fim_dia" required autocomplete="off"
                value="18:00"
                style="pointer-events: auto !important; user-select: auto !important;">
            </div>
          </div>

          <div class="mb-3">
            <label for="intervalo_dia" class="form-label text-white">Intervalo entre horários (minutos)</label>
            <select class="form-select bg-dark text-white border-secondary modal-input-interactive"
              id="intervalo_dia" name="intervalo_dia" autocomplete="off"
              style="pointer-events: auto !important; user-select: auto !important;">
              <option value="15">15 minutos</option>
              <option value="30" selected>30 minutos</option>
              <option value="45">45 minutos</option>
              <option value="60">60 minutos</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-dark text-white border-top border-secondary">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i>
            Cancelar
          </button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-lg me-1"></i>
            Criar Horários
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CSS específico para modais de horários -->
<style>
  /* Modais sem backdrop - totalmente interativos */
  .modal {
    z-index: 1055 !important;
    background-color: transparent !important;
  }

  /* Garantir que os modais funcionem corretamente */
  .modal.show {
    display: block !important;
  }

  .modal.show .modal-dialog {
    transform: none !important;
  }

  /* Inputs interativos - estilo específico para nova classe */
  .modal-input-interactive {
    background-color: rgba(40, 40, 40, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    transition: all 0.3s ease !important;
    pointer-events: auto !important;
    user-select: auto !important;
    cursor: text !important;
  }

  .modal-input-interactive:focus {
    background-color: rgba(50, 50, 50, 0.95) !important;
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    color: white !important;
    outline: none !important;
  }

  .modal-input-interactive::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
  }

  .modal-input-interactive:hover {
    border-color: rgba(255, 255, 255, 0.3) !important;
  }

  /* Checkboxes específicos */
  .modal-input-interactive[type="checkbox"] {
    cursor: pointer !important;
    background-color: rgba(40, 40, 40, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
  }

  .modal-input-interactive[type="checkbox"]:checked {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
  }

  /* Select específico */
  .modal-input-interactive option {
    background-color: rgba(40, 40, 40, 0.95) !important;
    color: white !important;
  }

  /* Garantir interatividade total para todos os elementos dos modais */
  .modal * {
    pointer-events: auto !important;
    user-select: auto !important;
  }

  .modal input,
  .modal textarea,
  .modal select,
  .modal button {
    pointer-events: auto !important;
    user-select: auto !important;
    cursor: auto !important;
  }

  .modal input[type="text"],
  .modal input[type="date"],
  .modal input[type="time"],
  .modal textarea {
    cursor: text !important;
  }

  .modal input[type="checkbox"] {
    cursor: pointer !important;
  }

  .modal button {
    cursor: pointer !important;
  }

  /* Override de qualquer CSS que possa estar bloqueando */
  #modalCriarSemana,
  #modalCriarSemana *,
  #modalCriarSemana input,
  #modalCriarSemana select,
  #modalCriarSemana button,
  #modalCriarDia,
  #modalCriarDia *,
  #modalCriarDia input,
  #modalCriarDia select,
  #modalCriarDia button {
    pointer-events: auto !important;
    user-select: auto !important;
  }

  /* Modal dialog com backdrop visual suave opcional */
  .modal-dialog {
    pointer-events: auto !important;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5) !important;
    border-radius: 8px !important;
  }

  /* Garantir que toda a página continue interativa quando modal está aberto */
  body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
  }

  /* Estilos específicos para form-check em tema escuro */
  .form-check-label {
    color: rgba(255, 255, 255, 0.9) !important;
    cursor: pointer !important;
  }

  .form-check-input {
    background-color: rgba(40, 40, 40, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
  }

  .form-check-input:checked {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
  }

  .form-check-input:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
  }
</style>

<!-- JavaScript específico para modais de horários -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalSemana = document.getElementById('modalCriarSemana');
    const modalDia = document.getElementById('modalCriarDia');

    // Função para configurar interatividade dos inputs
    function configurarModal(modal) {
      const inputs = modal.querySelectorAll('input, select, textarea');

      inputs.forEach(input => {
        input.style.pointerEvents = 'auto';
        input.style.userSelect = 'auto';

        if (input.type === 'checkbox') {
          input.style.cursor = 'pointer';
        } else {
          input.style.cursor = input.type === 'text' || input.type === 'date' || input.type === 'time' || input.tagName === 'TEXTAREA' ? 'text' : 'auto';
        }

        // Remover qualquer bloqueio de eventos
        input.addEventListener('click', function(e) {
          e.stopPropagation();
          if (this.type !== 'checkbox') {
            this.focus();
          }
        });

        input.addEventListener('focus', function() {
          if (this.type === 'checkbox') return;
          this.style.borderColor = '#0d6efd';
          this.style.boxShadow = '0 0 0 0.2rem rgba(13, 110, 253, 0.25)';
        });

        input.addEventListener('blur', function() {
          if (this.type === 'checkbox') return;
          this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
          this.style.boxShadow = 'none';
        });
      });
    }

    // Configurar modal da semana
    const btnAbrirModalSemana = document.querySelector('[data-bs-target="#modalCriarSemana"]');
    if (btnAbrirModalSemana) {
      btnAbrirModalSemana.addEventListener('click', function(e) {
        e.preventDefault();

        const modalInstance = new bootstrap.Modal(modalSemana, {
          backdrop: false,
          keyboard: true
        });

        modalInstance.show();

        setTimeout(() => {
          const primeiroInput = modalSemana.querySelector('#data_inicio');
          if (primeiroInput) {
            primeiroInput.focus();
          }
        }, 300);
      });
    }

    // Configurar modal do dia
    const btnAbrirModalDia = document.querySelector('[data-bs-target="#modalCriarDia"]');
    if (btnAbrirModalDia) {
      btnAbrirModalDia.addEventListener('click', function(e) {
        e.preventDefault();

        const modalInstance = new bootstrap.Modal(modalDia, {
          backdrop: false,
          keyboard: true
        });

        modalInstance.show();

        setTimeout(() => {
          const primeiroInput = modalDia.querySelector('#data');
          if (primeiroInput) {
            primeiroInput.focus();
          }
        }, 300);
      });
    }

    // Configurar interatividade para ambos os modais
    configurarModal(modalSemana);
    configurarModal(modalDia);

    // Fechar modais ao clicar fora deles (já que não temos backdrop)
    document.addEventListener('click', function(e) {
      [modalSemana, modalDia].forEach(modal => {
        if (modal.classList.contains('show')) {
          const modalDialog = modal.querySelector('.modal-dialog');
          if (!modalDialog.contains(e.target)) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
              modalInstance.hide();
            }
          }
        }
      });
    });

    // Fechar modais com ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        [modalSemana, modalDia].forEach(modal => {
          if (modal.classList.contains('show')) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
              modalInstance.hide();
            }
          }
        });
      }
    });

    // Log para debug
    console.log('Modais de horários (sem backdrop) configurados corretamente');
    console.log('Modal Semana inputs:', modalSemana.querySelectorAll('input, select').length);
    console.log('Modal Dia inputs:', modalDia.querySelectorAll('input, select').length);
  });
</script>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>