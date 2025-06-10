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
    <a href="/barbeiro/dashboard.php" class="btn btn-outline-secondary">
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
<div class="modal fade" id="modalCriarSemana" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Criar Agenda Semanal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="acao" value="criar_semana">

          <div class="mb-3">
            <label for="data_inicio" class="form-label">Data de Início</label>
            <input type="date" class="form-control" id="data_inicio" name="data_inicio"
              value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="horario_inicio" class="form-label">Horário de Início</label>
              <input type="time" class="form-control" id="horario_inicio" name="horario_inicio"
                value="08:00" required>
            </div>
            <div class="col-md-6">
              <label for="horario_fim" class="form-label">Horário de Fim</label>
              <input type="time" class="form-control" id="horario_fim" name="horario_fim"
                value="18:00" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="intervalo" class="form-label">Intervalo entre horários (minutos)</label>
            <select class="form-select" id="intervalo" name="intervalo">
              <option value="15">15 minutos</option>
              <option value="30" selected>30 minutos</option>
              <option value="45">45 minutos</option>
              <option value="60">60 minutos</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Dias da Semana</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="1" id="seg">
              <label class="form-check-label" for="seg">Segunda-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="2" id="ter">
              <label class="form-check-label" for="ter">Terça-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="3" id="qua">
              <label class="form-check-label" for="qua">Quarta-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="4" id="qui">
              <label class="form-check-label" for="qui">Quinta-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="5" id="sex">
              <label class="form-check-label" for="sex">Sexta-feira</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="6" id="sab">
              <label class="form-check-label" for="sab">Sábado</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="dias_semana[]" value="0" id="dom">
              <label class="form-check-label" for="dom">Domingo</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Criar Horários</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Criar Dia -->
<div class="modal fade" id="modalCriarDia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Criar Agenda Específica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="acao" value="criar_dia">

          <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" class="form-control" id="data" name="data"
              value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="horario_inicio_dia" class="form-label">Horário de Início</label>
              <input type="time" class="form-control" id="horario_inicio_dia" name="horario_inicio_dia"
                value="08:00" required>
            </div>
            <div class="col-md-6">
              <label for="horario_fim_dia" class="form-label">Horário de Fim</label>
              <input type="time" class="form-control" id="horario_fim_dia" name="horario_fim_dia"
                value="18:00" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="intervalo_dia" class="form-label">Intervalo entre horários (minutos)</label>
            <select class="form-select" id="intervalo_dia" name="intervalo_dia">
              <option value="15">15 minutos</option>
              <option value="30" selected>30 minutos</option>
              <option value="45">45 minutos</option>
              <option value="60">60 minutos</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Criar Horários</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>