<?php
$title = 'Cancelar Agendamento';
$breadcrumb = [
  ['title' => 'Dashboard', 'url' => BASE_URL . 'cliente/dashboard.php'],
  ['title' => 'Agendamentos', 'url' => BASE_URL . 'cliente/agendamentos.php'],
  ['title' => 'Cancelar Agendamento']
];
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../partials/components.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header bg-transparent">
        <h3 class="mb-0"><i class="bi bi-x-circle text-danger"></i> Cancelar Agendamento</h3>
      </div>
      <div class="card-body">
        <div class="alert alert-warning mb-4">
          <strong>Atenção:</strong> Esta ação não pode ser desfeita. O agendamento será cancelado permanentemente.
        </div>

        <!-- Detalhes do agendamento -->
        <div class="row mb-4">
          <div class="col-md-6">
            <h6 class="text-gold mb-3">Detalhes do Agendamento</h6>
            <table class="table table-sm" style="background: rgba(30, 30, 30, 0.4); border-radius: var(--border-radius); overflow: hidden;">
              <tr>
                <td><strong>Barbeiro:</strong></td>
                <td><?= htmlspecialchars($agendamento['barbeiro_nome']) ?></td>
              </tr>
              <tr>
                <td><strong>Serviço:</strong></td>
                <td><?= htmlspecialchars($agendamento['servico_nome']) ?></td>
              </tr>
              <tr>
                <td><strong>Data/Hora:</strong></td>
                <td><?= formatDateBR($agendamento['data_hora']) ?></td>
              </tr>
              <tr>
                <td><strong>Valor:</strong></td>
                <td><?= formatPrice($agendamento['servico_preco']) ?></td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td><?= getStatusBadge($agendamento['status']) ?></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="text-gold mb-3">Motivo do Cancelamento</h6>
            <form action="" method="post">
              <div class="form-group mb-4">
                <textarea name="motivo_cancelamento" class="form-control" rows="5" placeholder="Informe o motivo do cancelamento (opcional)"></textarea>
                <div class="form-text">O motivo do cancelamento ajuda nossos barbeiros a melhorarem o atendimento.</div>
              </div>

              <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>cliente/agendamentos.php" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-2"></i>
                  Voltar
                </a>
                <button type="submit" class="btn btn-danger">
                  <i class="bi bi-x-circle me-2"></i>
                  Confirmar Cancelamento
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>