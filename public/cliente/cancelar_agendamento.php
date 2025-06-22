<?php

/**
 * Página para cancelar agendamento específico
 * Recebe ID do agendamento via GET e processa cancelamento via POST
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ClienteController.php';

initApp();

$controller = new ClienteController();
$controller->cancelarAgendamento();
?>

$data_agendamento = new DateTime($dados['data_hora']);
$agora = new DateTime();

if ($data_agendamento < $agora) {
  setFlashMessage('Não é possível cancelar agendamentos passados.', 'error' );
  redirect(BASE_URL . '/cliente/agendamentos.php' );
  }

  // Processar cancelamento
  if ($_POST) {
  try {
  $resultado=$agendamento->update($agendamento_id, [
  'status' => 'cancelado'
  ]);

  if ($resultado) {
  // Reativar horário disponível
  $horario = new HorarioDisponivel();
  $data_agendamento = date('Y-m-d', strtotime($dados['data_hora']));
  $horario_agendamento = date('H:i:s', strtotime($dados['data_hora']));
  $horario->reativarHorario($dados['barbeiro_id'], $data_agendamento, $horario_agendamento);

  setFlashMessage('Agendamento cancelado com sucesso!', 'success');
  } else {
  setFlashMessage('Erro ao cancelar agendamento.', 'error');
  }
  } catch (Exception $e) {
  error_log("Erro ao cancelar agendamento: " . $e->getMessage());
  setFlashMessage('Erro interno. Tente novamente.', 'error');
  }

  redirect(BASE_URL . '/cliente/agendamentos.php');
  }

  // Buscar dados do barbeiro e serviço para exibir
  $barbeiro = new Barbeiro();
  $dados_barbeiro = $barbeiro->findById($dados['barbeiro_id']);

  $servico = new Servico();
  $dados_servico = $servico->findById($dados['servico_id']);

  include BASE_URL . '../views/layouts/header.php';
  ?>

  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/cliente/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/cliente/agendamentos.php">Meus Agendamentos</a></li>
            <li class="breadcrumb-item active">Cancelar Agendamento</li>
          </ol>
        </nav>

        <?php include '../../views/partials/messages.php'; ?>

        <div class="card">
          <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
              <i class="bi bi-x-circle me-2"></i>
              Cancelar Agendamento
            </h5>
          </div>
          <div class="card-body">
            <div class="alert alert-warning">
              <i class="bi bi-exclamation-triangle me-2"></i>
              <strong>Atenção:</strong> Esta ação não pode ser desfeita. O agendamento será cancelado permanentemente.
            </div>

            <!-- Detalhes do agendamento -->
            <div class="row mb-4">
              <div class="col-md-6">
                <h6 class="text-gold mb-3">Detalhes do Agendamento</h6>
                <table class="table table-sm" style="background: rgba(30, 30, 30, 0.4); border-radius: var(--border-radius); overflow: hidden;">
                  <tr>
                    <td><strong>Barbeiro:</strong></td>
                    <td><?= htmlspecialchars($dados_barbeiro['nome']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Serviço:</strong></td>
                    <td><?= htmlspecialchars($dados_servico['nome']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Data:</strong></td>
                    <td><?= formatDate(date('Y-m-d', strtotime($dados['data_hora']))) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Horário:</strong></td>
                    <td><?= date('H:i', strtotime($dados['data_hora'])) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Valor:</strong></td>
                    <td>R$ <?= number_format($dados_servico['preco'], 2, ',', '.') ?></td>
                  </tr>
                  <tr>
                    <td><strong>Status:</strong></td>
                    <td><?= getStatusBadge($dados['status']) ?></td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Formulário de cancelamento -->
            <form method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?')">
              <div class="d-flex justify-content-between">
                <a href="/cliente/agendamentos.php" class="btn btn-secondary">
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

  <?php include '../../views/layouts/footer.php'; ?>