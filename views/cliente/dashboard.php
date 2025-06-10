<?php
$title = 'Dashboard Cliente';
$breadcrumb = [
  ['title' => 'Início', 'url' => BASE_URL],
  ['title' => 'Dashboard']
];
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../partials/components.php';
?>

<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Bem-vindo(a), <?= $_SESSION['user_name'] ?>!</p>
      </div>
      <div>
        <a href="<?= BASE_URL ?>cliente/novo_agendamento.php" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Novo Agendamento
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Cards de Ações Rápidas -->
  <div class="col-md-4 mb-4">
    <div class="card text-center card-hover h-100">
      <div class="card-body">
        <div class="text-primary mb-3">
          <i class="bi bi-calendar-plus fs-1"></i>
        </div>
        <h5 class="card-title">Novo Agendamento</h5>
        <p class="card-text">Agende um novo serviço com nossos barbeiros</p>
        <a href="<?= BASE_URL ?>cliente/novo_agendamento.php" class="btn btn-primary">Agendar</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="card text-center card-hover h-100">
      <div class="card-body">
        <div class="text-info mb-3">
          <i class="bi bi-calendar-check fs-1"></i>
        </div>
        <h5 class="card-title">Meus Agendamentos</h5>
        <p class="card-text">Visualize e gerencie seus agendamentos</p>
        <a href="<?= BASE_URL ?>cliente/agendamentos.php" class="btn btn-info">Ver Agendamentos</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="card text-center card-hover h-100">
      <div class="card-body">
        <div class="text-secondary mb-3">
          <i class="bi bi-person-gear fs-1"></i>
        </div>
        <h5 class="card-title">Meu Perfil</h5>
        <p class="card-text">Atualize suas informações pessoais</p>
        <a href="<?= BASE_URL ?>cliente/perfil.php" class="btn btn-secondary">Editar Perfil</a>
      </div>
    </div>
  </div>
</div>

<!-- Próximos Agendamentos -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="bi bi-calendar-event"></i> Próximos Agendamentos
        </h5>
        <a href="<?= BASE_URL ?>cliente/agendamentos.php" class="btn btn-outline-primary btn-sm">Ver Todos</a>
      </div>
      <div class="card-body">
        <?php if (empty($proximosAgendamentos)): ?>
          <?php renderEmptyState(
            'Você não tem agendamentos próximos',
            'calendar-x',
            '<a href="' . BASE_URL . 'cliente/novo_agendamento.php" class="btn btn-primary">Agendar Agora</a>'
          ); ?>
        <?php else: ?>
          <div class="table-container">
            <table class="table table-hover agendamentos-table">
              <thead>
                <tr>
                  <th>Data/Hora</th>
                  <th>Serviço</th>
                  <th>Barbeiro</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($proximosAgendamentos as $agendamento): ?>
                  <tr>
                    <td>
                      <strong><?= formatDateBR($agendamento['data_hora']) ?></strong>
                    </td>
                    <td>
                      <?= htmlspecialchars($agendamento['servico_nome']) ?>
                    </td>
                    <td>
                      <i class="bi bi-person"></i>
                      <?= htmlspecialchars($agendamento['barbeiro_nome']) ?>
                    </td>
                    <td>
                      <?= getStatusBadge($agendamento['status']) ?>
                    </td>
                    <td>
                      <?= getAgendamentoActions($agendamento, 'cliente') ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>