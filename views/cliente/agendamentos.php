<?php
$title = 'Meus Agendamentos';
$breadcrumb = [
  ['title' => 'Dashboard', 'url' => BASE_URL . 'cliente/dashboard.php'],
  ['title' => 'Meus Agendamentos']
];
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../partials/components.php';
?>
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2><i class="bi bi-calendar-check"></i> Meus Agendamentos</h2>
        <p class="text-muted">Visualize e gerencie todos os seus agendamentos</p>
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
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <?php if (empty($agendamentos)): ?>
          <?php renderEmptyState(
            'Você ainda não possui agendamentos',
            'calendar-x',
            '<a href="' . BASE_URL . 'cliente/novo_agendamento.php" class="btn btn-primary">Fazer Primeiro Agendamento</a>'
          ); ?>
        <?php else: ?>
          <div class="table-container">
            <table class="table table-hover agendamentos-table">
              <thead>
                <tr>
                  <th>Data/Hora</th>
                  <th>Serviço</th>
                  <th>Barbeiro</th>
                  <th>Preço</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($agendamentos as $agendamento): ?>
                  <tr>
                    <td>
                      <div>
                        <strong><?= date('d/m/Y H:i', strtotime($agendamento['data_hora'])) ?></strong>
                      </div>
                    </td>
                    <td>
                      <div>
                        <strong><?= htmlspecialchars($agendamento['servico_nome']) ?></strong>
                      </div>
                    </td>
                    <td>
                      <div>
                        <i class="bi bi-person"></i>
                        <?= htmlspecialchars($agendamento['barbeiro_nome']) ?>
                      </div>
                    </td>
                    <td>
                      <strong class="text-success">
                        <?= isset($agendamento['servico_preco']) ? formatPrice($agendamento['servico_preco']) : '' ?>
                      </strong>
                    </td>
                    <td>
                      <?= getStatusBadge($agendamento['status']) ?>
                    </td>
                    <td>
                      <?php
                      // Só exibe ações se não estiver finalizado ou cancelado
                      if (!in_array($agendamento['status'], ['finalizado', 'cancelado'])) {
                        echo getAgendamentoActions($agendamento, 'cliente');
                      }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="mt-3 p-3 rounded" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
            <h6 class="text-gold mb-3">Legenda de Status:</h6>
            <div class="row">
              <div class="col-md-4 mb-2">
                <?= getStatusBadge('pendente') ?> - Aguardando confirmação do barbeiro
              </div>
              <div class="col-md-4 mb-2">
                <?= getStatusBadge('confirmado') ?> - Agendamento confirmado
              </div>
              <div class="col-md-4 mb-2">
                <?= getStatusBadge('cancelado') ?> - Agendamento cancelado
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>