<?php
require_once __DIR__ . '/../../config/config.php';

initApp();

checkBarberAuth();

$barbeiro_id = $_SESSION['barbeiro_id'];

// Processar formulário
if ($_POST) {
  $acao = $_POST['acao'] ?? '';
  $servico = new Servico();

  if ($acao === 'criar') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';
    $duracao = $_POST['duracao'] ?? '';

    $erros = [];

    // Validações
    if (empty($nome)) {
      $erros[] = 'Nome do serviço é obrigatório.';
    }

    if (empty($preco) || !is_numeric($preco) || $preco <= 0) {
      $erros[] = 'Preço deve ser um valor válido maior que zero.';
    }

    if (empty($duracao) || !is_numeric($duracao) || $duracao <= 0) {
      $erros[] = 'Duração deve ser um valor válido em minutos.';
    }

    if (empty($erros)) {
      try {
        $dados = [
          'barbeiro_id' => $barbeiro_id,
          'nome' => $nome,
          'descricao' => $descricao ?: null,
          'preco' => (float)$preco,
          'duracao' => (int)$duracao,
          'ativo' => 1
        ];

        $resultado = $servico->create($dados);
        if ($resultado) {
          setFlashMessage('Serviço criado com sucesso!', 'success');
        } else {
          setFlashMessage('Erro ao criar serviço.', 'error');
        }
      } catch (Exception $e) {
        error_log("Erro ao criar serviço: " . $e->getMessage());
        setFlashMessage('Erro interno. Tente novamente.', 'error');
      }
    } else {
      foreach ($erros as $erro) {
        setFlashMessage($erro, 'error');
      }
    }
  } elseif ($acao === 'editar') {
    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';
    $duracao = $_POST['duracao'] ?? '';

    // Verificar se serviço pertence ao barbeiro
    $dados_servico = $servico->findById($id);
    if (!$dados_servico || $dados_servico['barbeiro_id'] != $barbeiro_id) {
      setFlashMessage('Serviço não encontrado.', 'error');
    } else {
      $erros = [];

      // Validações
      if (empty($nome)) {
        $erros[] = 'Nome do serviço é obrigatório.';
      }

      if (empty($preco) || !is_numeric($preco) || $preco <= 0) {
        $erros[] = 'Preço deve ser um valor válido maior que zero.';
      }

      if (empty($duracao) || !is_numeric($duracao) || $duracao <= 0) {
        $erros[] = 'Duração deve ser um valor válido em minutos.';
      }

      if (empty($erros)) {
        try {
          $dados = [
            'nome' => $nome,
            'descricao' => $descricao ?: null,
            'preco' => (float)$preco,
            'duracao' => (int)$duracao
          ];

          $resultado = $servico->update($id, $dados);
          if ($resultado) {
            setFlashMessage('Serviço atualizado com sucesso!', 'success');
          } else {
            setFlashMessage('Erro ao atualizar serviço.', 'error');
          }
        } catch (Exception $e) {
          error_log("Erro ao atualizar serviço: " . $e->getMessage());
          setFlashMessage('Erro interno. Tente novamente.', 'error');
        }
      } else {
        foreach ($erros as $erro) {
          setFlashMessage($erro, 'error');
        }
      }
    }
  } elseif ($acao === 'toggle_status') {
    $id = $_POST['id'] ?? '';

    // Verificar se serviço pertence ao barbeiro
    $dados_servico = $servico->findById($id);
    if (!$dados_servico || $dados_servico['barbeiro_id'] != $barbeiro_id) {
      setFlashMessage('Serviço não encontrado.', 'error');
    } else {
      try {
        $novo_status = $dados_servico['ativo'] ? 0 : 1;
        $resultado = $servico->update($id, ['ativo' => $novo_status]);

        if ($resultado) {
          $msg = $novo_status ? 'Serviço ativado' : 'Serviço desativado';
          setFlashMessage($msg . ' com sucesso!', 'success');
        } else {
          setFlashMessage('Erro ao alterar status do serviço.', 'error');
        }
      } catch (Exception $e) {
        error_log("Erro ao alterar status do serviço: " . $e->getMessage());
        setFlashMessage('Erro interno. Tente novamente.', 'error');
      }
    }
  } elseif ($acao === 'excluir') {
    $id = $_POST['id'] ?? '';
    // Verificar se serviço pertence ao barbeiro
    $dados_servico = $servico->findById($id);
    if (!$dados_servico || $dados_servico['barbeiro_id'] != $barbeiro_id) {
      setFlashMessage('Serviço não encontrado.', 'error');
    } else {
      try {
        $servico->delete($id);
        setFlashMessage('Serviço excluído com sucesso!', 'success');
      } catch (Exception $e) {
        error_log("Erro ao excluir serviço: " . $e->getMessage());
        setFlashMessage('Erro ao excluir serviço.', 'error');
      }
    }
  }

  redirect('/barbeiro/servicos.php');
}

// Buscar serviços do barbeiro
$servico = new Servico();
$meus_servicos = $servico->getByBarbeiroId($barbeiro_id);

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/partials/components.php';
?>

<div class="container mt-4">
  <!-- Cabeçalho -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0">Meus Serviços</h1>
      <p class="text-muted">Gerencie os serviços que você oferece</p>
    </div>
    <div>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoServico">
        <i class="bi bi-plus-lg me-2"></i>
        Novo Serviço
      </button>
      <a href="/barbeiro/dashboard.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Dashboard
      </a>
    </div>
  </div>

  <?php include __DIR__ . '/../../views/partials/messages.php'; ?>

  <!-- Lista de Serviços -->
  <div class="row">
    <?php if (empty($meus_servicos)): ?>
      <div class="col-12">
        <?= getEmptyState('Nenhum serviço cadastrado', 'Adicione serviços para que os clientes possam fazer agendamentos.') ?>
      </div>
    <?php else: ?>
      <?php foreach ($meus_servicos as $serv): ?>
        <div class="col-md-6 col-lg-4 mb-3">
          <div class="card h-100 <?= $serv['ativo'] ? '' : 'bg-light' ?>">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="card-title"><?= htmlspecialchars($serv['nome']) ?></h5>
                <span class="badge <?= $serv['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                  <?= $serv['ativo'] ? 'Ativo' : 'Inativo' ?>
                </span>
              </div>

              <?php if ($serv['descricao']): ?>
                <p class="card-text text-muted"><?= htmlspecialchars($serv['descricao']) ?></p>
              <?php endif; ?>

              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-primary mb-0">R$ <?= number_format($serv['preco'], 2, ',', '.') ?></h6>
                  <small class="text-muted"><?= $serv['duracao'] ?> minutos</small>
                </div>

                <div class="btn-group btn-group-sm">
                  <form method="POST" class="d-inline" onsubmit="return confirm('Alterar status deste serviço?')">
                    <input type="hidden" name="acao" value="toggle_status">
                    <input type="hidden" name="id" value="<?= $serv['id'] ?>">
                    <button type="submit" class="btn <?= $serv['ativo'] ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                      title="<?= $serv['ativo'] ? 'Desativar' : 'Ativar' ?>">
                      <i class="bi <?= $serv['ativo'] ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                  </form>
                  <!-- Botão Excluir Serviço -->
                  <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.')">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?= $serv['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger" title="Excluir">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Novo Serviço -->
<div class="modal fade" id="modalNovoServico" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Novo Serviço</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="acao" value="criar">

          <div class="mb-3">
            <label for="nome" class="form-label">Nome do Serviço</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
          </div>

          <div class="mb-3">
            <label for="descricao" class="form-label">Descrição (opcional)</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="preco" class="form-label">Preço (R$)</label>
              <input type="number" class="form-control" id="preco" name="preco"
                step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
              <label for="duracao" class="form-label">Duração (minutos)</label>
              <input type="number" class="form-control" id="duracao" name="duracao"
                min="1" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Criar Serviço</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>