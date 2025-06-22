<?php
require_once __DIR__ . '/../../config/config.php';

initApp();

checkBarberAuth();

$barbeiro_id = $_SESSION['barbeiro_id'];

// Filtros
$data_filtro = $_GET['data'] ?? '';
$status_filtro = $_GET['status'] ?? '';
$page = (int)($_GET['page'] ?? 1);

// Processar ações (confirmar/cancelar)
if ($_POST) {
  $acao = $_POST['acao'] ?? '';
  $agendamento_id = $_POST['agendamento_id'] ?? '';

  if ($acao && $agendamento_id) {
    $agendamento = new Agendamento();
    $dados_agendamento = $agendamento->findById($agendamento_id);

    // Verificar se agendamento pertence ao barbeiro
    if ($dados_agendamento && $dados_agendamento['barbeiro_id'] == $barbeiro_id) {
      try {
        if ($acao === 'confirmar' && $dados_agendamento['status'] === 'pendente') {
          $resultado = $agendamento->update($agendamento_id, ['status' => 'confirmado']);
          if ($resultado) {
            setFlashMessage('Agendamento confirmado com sucesso!', 'success');
          } else {
            setFlashMessage('Erro ao confirmar agendamento.', 'error');
          }
        } elseif ($acao === 'cancelar' && in_array($dados_agendamento['status'], ['pendente', 'confirmado'])) {
          $resultado = $agendamento->update($agendamento_id, ['status' => 'cancelado']);
          if ($resultado) {
            // Reativar horário
            $horario = new HorarioDisponivel();
            $data_agendamento = date('Y-m-d', strtotime($dados_agendamento['data_hora']));
            $horario_agendamento = date('H:i:s', strtotime($dados_agendamento['data_hora']));
            $horario->reativarHorario($barbeiro_id, $data_agendamento, $horario_agendamento);
            setFlashMessage('Agendamento cancelado com sucesso!', 'success');
          } else {
            setFlashMessage('Erro ao cancelar agendamento.', 'error');
          }
        } elseif ($acao === 'finalizar' && $dados_agendamento['status'] === 'confirmado') {
          // Atualizar status para finalizado
          $resultado = $agendamento->update($agendamento_id, ['status' => 'finalizado']);
          if ($resultado) {
            setFlashMessage('Agendamento finalizado com sucesso!', 'success');
          } else {
            setFlashMessage('Erro ao finalizar agendamento.', 'error');
          }
        }
        // Redirecionar para evitar reenvio de formulário e limpar URL
        header('Location: agendamentos.php');
        exit;
      } catch (Exception $e) {
        error_log("Erro ao processar ação no agendamento: " . $e->getMessage());
        setFlashMessage('Erro interno. Tente novamente.', 'error');
        header('Location: agendamentos.php');
        exit;
      }
    } else {
      setFlashMessage('Agendamento não encontrado.', 'error');
      header('Location: agendamentos.php');
      exit;
    }
  }

  redirect("/barbeiro/agendamentos.php" . buildQueryString(['data' => $data_filtro, 'status' => $status_filtro, 'page' => $page]));
}

// Buscar agendamentos
$agendamento = new Agendamento();
$filtros = [
  'barbeiro_id' => $barbeiro_id
];

if ($data_filtro) {
  $filtros['data_inicio'] = $data_filtro;
  $filtros['data_fim'] = $data_filtro;
}

if ($status_filtro) {
  $filtros['status'] = $status_filtro;
}

$per_page = 10;
$offset = ($page - 1) * $per_page;

$agendamentos = $agendamento->getAgendamentosWithDetails($filtros, $offset, $per_page);
$total_agendamentos = $agendamento->countAgendamentos($filtros);
$total_pages = ceil($total_agendamentos / $per_page);

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/partials/components.php';
?>

<div class="container mt-4">
  <!-- Cabeçalho -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0">Meus Agendamentos</h1>
      <p class="text-muted">Gerencie todos os seus agendamentos</p>
    </div>
    <a href="<?= BASE_URL ?>barbeiro/dashboard.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>
      Voltar ao Dashboard
    </a>
  </div>

  <?php include '../../views/partials/messages.php'; ?>

  <!-- Filtros -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-3">
          <label for="data" class="form-label">Data</label>
          <input type="date" class="form-control" id="data" name="data" value="<?= htmlspecialchars($data_filtro) ?>">
        </div>
        <div class="col-md-3">
          <label for="status" class="form-label">Status</label>
          <select class="form-select" id="status" name="status">
            <option value="">Todos os status</option>
            <option value="pendente" <?= $status_filtro === 'pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="confirmado" <?= $status_filtro === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
            <option value="finalizado" <?= $status_filtro === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
            <option value="cancelado" <?= $status_filtro === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
          </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-search me-2"></i>
            Filtrar
          </button>
          <a href="<?= BASE_URL ?>barbeiro/agendamentos.php" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
        <div class="col-md-3 text-end">
          <small class="text-muted">
            Total: <?= $total_agendamentos ?> agendamento(s)
          </small>
        </div>
      </form>
    </div>
  </div>

  <!-- Lista de Agendamentos -->
  <div class="card">
    <div class="card-body">
      <?php if (empty($agendamentos)): ?>
        <?= getEmptyState('Nenhum agendamento encontrado', 'Não há agendamentos que correspondam aos filtros selecionados.') ?>
      <?php else: ?>
        <div class="table-container">
          <table class="table table-hover agendamentos-table">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Data/Hora</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($agendamentos as $agend): ?>
                <tr>
                  <td>
                    <div>
                      <strong><?= htmlspecialchars($agend['cliente_nome']) ?></strong><br>
                      <small class="text-muted"><?= htmlspecialchars($agend['cliente_telefone']) ?></small>
                    </div>
                  </td>
                  <td>
                    <?= htmlspecialchars($agend['servico_nome']) ?><br>
                    <small class="text-muted"><?= $agend['servico_duracao'] ?>min</small>
                  </td>
                  <td>
                    <?= formatDate($agend['data_agendamento']) ?><br>
                    <strong><?= date('H:i', strtotime($agend['horario'])) ?></strong>
                  </td>
                  <td>
                    <strong>R$ <?= number_format($agend['servico_preco'], 2, ',', '.') ?></strong>
                  </td>
                  <td>
                    <?= getStatusBadge($agend['status']) ?>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <?php if ($agend['status'] === 'pendente'): ?>
                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Confirmar este agendamento?')">
                          <input type="hidden" name="acao" value="confirmar">
                          <input type="hidden" name="agendamento_id" value="<?= $agend['id'] ?>">
                          <button type="submit" class="btn btn-outline-success btn-sm" title="Confirmar">
                            <i class="bi bi-check-lg"></i>
                          </button>
                        </form>
                      <?php endif; ?>

                      <?php if ($agend['status'] === 'confirmado'): ?>
                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Finalizar este agendamento?')">
                          <input type="hidden" name="acao" value="finalizar">
                          <input type="hidden" name="agendamento_id" value="<?= $agend['id'] ?>">
                          <button type="submit" class="btn btn-outline-primary btn-sm" title="Finalizar">
                            <i class="bi bi-check-circle"></i>
                          </button>
                        </form>
                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Cancelar este agendamento?')">
                          <input type="hidden" name="acao" value="cancelar">
                          <input type="hidden" name="agendamento_id" value="<?= $agend['id'] ?>">
                          <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancelar">
                            <i class="bi bi-x-lg"></i>
                          </button>
                        </form>
                      <?php endif; ?>
                      <!-- Não exibir nenhuma ação se status for 'finalizado' ou 'cancelado' -->
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
          <nav aria-label="Paginação">
            <ul class="pagination justify-content-center mb-0">
              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?= buildQueryString(['data' => $data_filtro, 'status' => $status_filtro, 'page' => $page - 1]) ?>">
                    Anterior
                  </a>
                </li>
              <?php endif; ?>

              <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                  <a class="page-link" href="?<?= buildQueryString(['data' => $data_filtro, 'status' => $status_filtro, 'page' => $i]) ?>">
                    <?= $i ?>
                  </a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $total_pages): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?= buildQueryString(['data' => $data_filtro, 'status' => $status_filtro, 'page' => $page + 1]) ?>">
                    Próximo
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>