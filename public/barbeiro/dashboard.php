<?php
require_once __DIR__ . '/../../config/config.php';

initApp();

checkBarberAuth();

$barbeiro_id = $_SESSION['barbeiro_id'];

// Buscar estatísticas
$agendamento = new Agendamento();
$servico = new Servico();

// === AGENDAMENTOS DA SEMANA ===
$hoje = date('Y-m-d');
$dia_semana = date('N'); // 1 (segunda) a 7 (domingo)
$inicio_semana = date('Y-m-d', strtotime($hoje . ' -' . ($dia_semana - 1) . ' days'));
$fim_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));
$agendamentos_semana = $agendamento->findByBarbeiroPeriodo($barbeiro_id, $inicio_semana, $fim_semana);
$stats_semana = $agendamento->getStatsByBarbeiro($barbeiro_id, $inicio_semana, $fim_semana);

// Próximos agendamentos (próximos 5)
$proximos_agendamentos = $agendamento->getProximosAgendamentos($barbeiro_id, 5);

// Serviços do barbeiro
$meus_servicos = $servico->findByBarbeiro($barbeiro_id);

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/partials/components.php';
?>
<div class="container mt-4">
  <!-- Cabeçalho -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0">Dashboard</h1>
      <p class="text-muted">Bem-vindo, <?= htmlspecialchars($_SESSION['barbeiro_nome'] ?? 'Barbeiro') ?>!</p>
    </div>
    <div class="text-end">
      <small class="text-muted">Semana: <?= formatDate($inicio_semana) ?> a <?= formatDate($fim_semana) ?></small>
    </div>
  </div>

  <?php include '../../views/partials/messages.php'; ?>

  <!-- Cards de Estatísticas -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h5 class="card-title">Esta Semana</h5>
              <h3><?= $stats_semana['total_agendamentos'] ?? 0 ?></h3>
              <p class="mb-0">Agendamentos</p>
            </div>
            <div class="align-self-center">
              <i class="bi bi-calendar-week fs-1"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h5 class="card-title">Serviços</h5>
              <h3><?= count($meus_servicos) ?></h3>
              <p class="mb-0">Cadastrados</p>
            </div>
            <div class="align-self-center">
              <i class="bi bi-scissors fs-1"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Agendamentos da Semana -->
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="bi bi-calendar-week me-2"></i>
            Agendamentos da Semana
          </h5>
          <a href="<?= BASE_URL ?>barbeiro/agendamentos.php" class="btn btn-sm btn-outline-primary">
            Ver Todos
          </a>
        </div>
        <div class="card-body">
          <?php if (empty($agendamentos_semana)): ?>
            <?= getEmptyState('Nenhum agendamento para esta semana', 'Você não possui agendamentos para esta semana.') ?>
          <?php else: ?>
            <div class="list-group list-group-flush">
              <?php foreach ($agendamentos_semana as $agend): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1"><?= htmlspecialchars($agend['cliente_nome']) ?></h6>
                    <p class="mb-1">
                      <strong><?= htmlspecialchars($agend['servico_nome']) ?></strong>
                      - <?= date('d/m H:i', strtotime($agend['data_hora'])) ?>
                    </p>
                    <small class="text-muted">
                      R$ <?= number_format($agend['servico_preco'], 2, ',', '.') ?>
                    </small>
                  </div>
                  <div class="text-end">
                    <?= getStatusBadge($agend['status']) ?>
                    <br>
                    <small class="text-muted">
                      <?= $agend['cliente_telefone'] ?>
                    </small>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Próximos Agendamentos -->
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bi bi-clock me-2"></i>
            Próximos Agendamentos
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($proximos_agendamentos)): ?>
            <small class="text-muted">Nenhum agendamento futuro</small>
          <?php else: ?>
            <?php foreach ($proximos_agendamentos as $agend): ?>
              <div class="border-start border-3 border-primary ps-3 mb-3">
                <h6 class="mb-1"><?= htmlspecialchars($agend['cliente_nome']) ?></h6>
                <small class="text-muted">
                  <?= formatDate($agend['data_agendamento']) ?> às <?= $agend['horario'] ?><br>
                  <?= htmlspecialchars($agend['servico_nome']) ?>
                </small>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Ações Rápidas -->
      <div class="card mt-3">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bi bi-lightning me-2"></i>
            Ações Rápidas
          </h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="<?= BASE_URL ?>barbeiro/agendamentos.php" class="btn btn-outline-primary btn-sm btn-text-white">
              <i class="bi bi-calendar-check me-2"></i>
              Ver Agendamentos
            </a>
            <a href="<?= BASE_URL ?>barbeiro/horarios.php" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-clock me-2"></i>
              Gerenciar Horários
            </a>
            <a href="<?= BASE_URL ?>barbeiro/servicos.php" class="btn btn-outline-info btn-sm">
              <i class="bi bi-scissors me-2"></i>
              Meus Serviços
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>