<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../views/partials/components.php';

initApp();

checkBarberAuth();

$barbeiro_id = $_SESSION['barbeiro_id'];

// Filtros
$busca = $_GET['busca'] ?? '';
$page = (int)($_GET['page'] ?? 1);

// Buscar clientes do barbeiro
$cliente = new Cliente();
$agendamento = new Agendamento();

$per_page = 10;
$offset = ($page - 1) * $per_page;

$clientes = $cliente->getClientesByBarbeiro($barbeiro_id, $busca, $offset, $per_page);
$total_clientes = $cliente->countClientesByBarbeiro($barbeiro_id, $busca);
$total_pages = ceil($total_clientes / $per_page);

include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="container mt-4">
  <!-- Cabeçalho -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0">Meus Clientes</h1>
      <p class="text-muted">Clientes que fizeram agendamentos comigo</p>
    </div>
    <a href="<?= BASE_URL ?>barbeiro/dashboard.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>
      Dashboard
    </a>
  </div>

  <?php include '../../views/partials/messages.php'; ?>

  <!-- Busca -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-8">
          <label for="busca" class="form-label">Buscar Cliente</label>
          <input type="text" class="form-control" id="busca" name="busca"
            value="<?= htmlspecialchars($busca) ?>"
            placeholder="Nome, telefone ou e-mail...">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-search me-2"></i>
            Buscar
          </button>
          <a href="<?= BASE_URL ?>barbeiro/clientes.php" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Lista de Clientes -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bi bi-people me-2"></i>
        Lista de Clientes
      </h5>
      <small class="text-muted">
        Total: <?= $total_clientes ?> cliente(s)
      </small>
    </div>
    <div class="card-body">
      <?php if (empty($clientes)): ?>
        <?= getEmptyState('Nenhum cliente encontrado', 'Não há clientes que correspondam à busca.') ?>
      <?php else: ?>
        <div class="row">
          <?php foreach ($clientes as $cli): ?>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                      style="width: 50px; height: 50px;">
                      <i class="bi bi-person fs-4"></i>
                    </div>
                    <div>
                      <h6 class="mb-0"><?= htmlspecialchars($cli['nome']) ?></h6>
                      <small class="text-muted">Cliente desde <?= formatDate($cli['data_cadastro']) ?></small>
                    </div>
                  </div>

                  <div class="mb-3">
                    <small class="text-muted">Contato:</small><br>
                    <div><?= htmlspecialchars($cli['telefone']) ?></div>
                    <div><?= htmlspecialchars($cli['email']) ?></div>
                  </div>

                  <div class="row text-center">
                    <div class="col-6">
                      <div class="border-end">
                        <h6 class="text-primary mb-0"><?= $cli['total_agendamentos'] ?></h6>
                        <small class="text-muted">Agendamentos</small>
                      </div>
                    </div>
                    <!-- Removido Total Gasto -->
                  </div>

                  <hr>

                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      Último: <?= $cli['ultimo_agendamento'] ? formatDate($cli['ultimo_agendamento']) : 'Nenhum' ?>
                    </small>
                    <!-- Removido botão Histórico -->
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
          <nav aria-label="Paginação">
            <ul class="pagination justify-content-center mb-0">
              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?= buildQueryString(['busca' => $busca, 'page' => $page - 1]) ?>">
                    Anterior
                  </a>
                </li>
              <?php endif; ?>

              <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                  <a class="page-link" href="?<?= buildQueryString(['busca' => $busca, 'page' => $i]) ?>">
                    <?= $i ?>
                  </a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $total_pages): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?= buildQueryString(['busca' => $busca, 'page' => $page + 1]) ?>">
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