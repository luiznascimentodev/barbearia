<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Barbeiro.php';
require_once __DIR__ . '/../../models/Servico.php';
require_once __DIR__ . '/../../models/HorarioDisponivel.php';

initApp();
checkClientAuth();

$barbeiro_id = $_GET['barbeiro_id'] ?? $_POST['barbeiro_id'] ?? '';
$servico_id = $_GET['servico_id'] ?? $_POST['servico_id'] ?? '';
$data_agendamento = $_GET['data_agendamento'] ?? $_POST['data_agendamento'] ?? '';
$horario_id = $_POST['horario_id'] ?? '';
$observacoes = $_POST['observacoes'] ?? '';

$barbeiroModel = new Barbeiro();
$servicoModel = new Servico();
$horarioModel = new HorarioDisponivel();

$barbeiros = $barbeiroModel->getBarbeirosAtivos();
$servicos = [];
$horarios = [];

if ($barbeiro_id) {
  $servicos = $servicoModel->findByBarbeiro($barbeiro_id);
}
if ($barbeiro_id && $servico_id && $data_agendamento) {
  $horarios = $horarioModel->findAvailableForBookingByBarbeiroServicoData($barbeiro_id, $servico_id, $data_agendamento);
}

// Se o formulário foi enviado para agendar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $barbeiro_id && $servico_id && $data_agendamento && $horario_id) {
  require_once __DIR__ . '/../../models/Agendamento.php';
  $agendamentoModel = new Agendamento();
  $horario = $horarioModel->findById($horario_id);
  if (!$horario || !$horario['disponivel']) {
    setFlashMessage('Horário não está mais disponível.', 'error');
  } else {
    $dataHora = $horario['data_hora'];
    $result = $agendamentoModel->createAgendamento($_SESSION['cliente_id'], $barbeiro_id, $servico_id, $dataHora, $observacoes);
    if ($result) {
      $horarioModel->markUnavailable($horario_id);
      setFlashMessage('Agendamento realizado com sucesso!', 'success');
      redirect(BASE_URL . '/cliente/dashboard.php');
    } else {
      setFlashMessage('Erro ao agendar. Tente novamente.', 'error');
    }
  }
}

$title = 'Novo Agendamento';
$breadcrumb = [
  ['title' => 'Dashboard', 'url' => BASE_URL . '/cliente/dashboard.php'],
  ['title' => 'Novo Agendamento']
];
include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/partials/components.php';
?>
<div class="row">
  <div class="col-12">
    <?php renderBackButton(BASE_URL . '/cliente/dashboard.php'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2><i class="bi bi-calendar-plus"></i> Novo Agendamento</h2>
        <p class="text-muted">Escolha o profissional, serviço, data e horário</p>
      </div>
    </div>
  </div>
</div>
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <form method="GET" class="mb-4">
          <div class="mb-3">
            <label for="barbeiro_id" class="form-label"><strong>Profissional</strong></label>
            <select class="form-select" id="barbeiro_id" name="barbeiro_id" required onchange="this.form.submit()">
              <option value="">Escolha o barbeiro...</option>
              <?php foreach ($barbeiros as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $barbeiro_id == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php if ($barbeiro_id): ?>
            <div class="mb-3">
              <label for="servico_id" class="form-label"><strong>Serviço</strong></label>
              <select class="form-select" id="servico_id" name="servico_id" required onchange="this.form.submit()">
                <option value="">Escolha o serviço...</option>
                <?php foreach ($servicos as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= $servico_id == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nome']) ?> - <?= formatPrice($s['preco']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
          <?php if ($barbeiro_id && $servico_id): ?>
            <div class="mb-3">
              <label for="data_agendamento" class="form-label"><strong>Data</strong></label>
              <input type="date" class="form-control" id="data_agendamento" name="data_agendamento" value="<?= htmlspecialchars($data_agendamento) ?>" min="<?= date('Y-m-d') ?>" required onchange="this.form.submit()">
            </div>
          <?php endif; ?>
        </form>
        <?php if ($barbeiro_id && $servico_id && $data_agendamento): ?>
          <form method="POST">
            <input type="hidden" name="barbeiro_id" value="<?= $barbeiro_id ?>">
            <input type="hidden" name="servico_id" value="<?= $servico_id ?>">
            <input type="hidden" name="data_agendamento" value="<?= $data_agendamento ?>">
            <div class="mb-3">
              <label for="horario_id" class="form-label"><strong>Horário</strong></label>
              <select class="form-select" id="horario_id" name="horario_id" required>
                <option value="">Escolha o horário...</option>
                <?php foreach ($horarios as $h): ?>
                  <option value="<?= $h['id'] ?>">
                    <?= date('H:i', strtotime($h['data_hora'])) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (empty($horarios)): ?>
                <div class="alert alert-warning mt-2">Nenhum horário disponível para esta data.</div>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label for="observacoes" class="form-label">Observações (opcional)</label>
              <textarea class="form-control" id="observacoes" name="observacoes" rows="2"></textarea>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle"></i> Confirmar Agendamento</button>
              <a href="<?= BASE_URL ?>/cliente/dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informações Importantes</h6>
      </div>
      <div class="card-body">
        <ul class="list-unstyled">
          <li><i class="bi bi-check text-success"></i> Os agendamentos ficam pendentes até a confirmação do barbeiro</li>
          <li><i class="bi bi-check text-success"></i> Você pode cancelar agendamentos até 2 horas antes do horário</li>
          <li><i class="bi bi-check text-success"></i> Você receberá uma confirmação após o agendamento</li>
          <li><i class="bi bi-check text-success"></i> Em caso de dúvidas, entre em contato diretamente com o barbeiro</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>