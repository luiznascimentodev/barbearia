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
      <a href="<?= BASE_URL ?>barbeiro/dashboard.php" class="btn btn-outline-secondary">
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
<div class="modal fade" id="modalNovoServico" tabindex="-1" aria-labelledby="modalNovoServicoLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary">
      <form method="POST" id="formNovoServico">
        <div class="modal-header bg-dark text-white border-bottom border-secondary">
          <h5 class="modal-title text-white" id="modalNovoServicoLabel">
            <i class="bi bi-scissors me-2 text-warning"></i>
            Novo Serviço
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body bg-dark text-white">
          <input type="hidden" name="acao" value="criar">

          <div class="mb-3">
            <label for="nomeServico" class="form-label text-white">Nome do Serviço</label>
            <input type="text" class="form-control bg-dark text-white border-secondary modal-input-interactive"
              id="nomeServico" name="nome" required autocomplete="off"
              placeholder="Ex: Corte masculino"
              style="pointer-events: auto !important; user-select: auto !important;">
          </div>

          <div class="mb-3">
            <label for="descricaoServico" class="form-label text-white">Descrição (opcional)</label>
            <textarea class="form-control bg-dark text-white border-secondary modal-input-interactive"
              id="descricaoServico" name="descricao" rows="3" autocomplete="off"
              placeholder="Descreva os detalhes do serviço..."
              style="pointer-events: auto !important; user-select: auto !important;"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="precoServico" class="form-label text-white">Preço (R$)</label>
              <input type="number" class="form-control bg-dark text-white border-secondary modal-input-interactive"
                id="precoServico" name="preco" step="0.01" min="0" required autocomplete="off"
                placeholder="0,00"
                style="pointer-events: auto !important; user-select: auto !important;">
            </div>
            <div class="col-md-6">
              <label for="duracaoServico" class="form-label text-white">Duração (minutos)</label>
              <input type="number" class="form-control bg-dark text-white border-secondary modal-input-interactive"
                id="duracaoServico" name="duracao" min="1" required autocomplete="off"
                placeholder="30"
                style="pointer-events: auto !important; user-select: auto !important;">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-dark text-white border-top border-secondary">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i>
            Cancelar
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>
            Criar Serviço
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CSS específico para modal -->
<style>
  /* Modal sem backdrop - totalmente interativo */
  .modal {
    z-index: 1055 !important;
    background-color: transparent !important;
  }

  /* Garantir que o modal funcione corretamente */
  .modal.show {
    display: block !important;
  }

  .modal.show .modal-dialog {
    transform: none !important;
  }

  /* Inputs interativos - estilo específico para nova classe */
  .modal-input-interactive {
    background-color: rgba(40, 40, 40, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    transition: all 0.3s ease !important;
    pointer-events: auto !important;
    user-select: auto !important;
    cursor: text !important;
  }

  .modal-input-interactive:focus {
    background-color: rgba(50, 50, 50, 0.95) !important;
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    color: white !important;
    outline: none !important;
  }

  .modal-input-interactive::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
  }

  .modal-input-interactive:hover {
    border-color: rgba(255, 255, 255, 0.3) !important;
  }

  /* Garantir interatividade total para todos os elementos do modal */
  .modal * {
    pointer-events: auto !important;
    user-select: auto !important;
  }

  .modal input,
  .modal textarea,
  .modal select,
  .modal button {
    pointer-events: auto !important;
    user-select: auto !important;
    cursor: auto !important;
  }

  .modal input[type="text"],
  .modal input[type="number"],
  .modal textarea {
    cursor: text !important;
  }

  .modal button {
    cursor: pointer !important;
  }

  /* Override de qualquer CSS que possa estar bloqueando */
  #modalNovoServico,
  #modalNovoServico *,
  #modalNovoServico input,
  #modalNovoServico textarea,
  #modalNovoServico button {
    pointer-events: auto !important;
    user-select: auto !important;
  }

  /* Estilos específicos para placeholders e foco */
  #modalNovoServico input::placeholder,
  #modalNovoServico textarea::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
    opacity: 1 !important;
  }

  /* Modal dialog com backdrop visual suave opcional */
  .modal-dialog {
    pointer-events: auto !important;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5) !important;
    border-radius: 8px !important;
  }

  /* Garantir que toda a página continue interativa quando modal está aberto */
  body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
  }
</style>

<!-- JavaScript específico para modal de serviços -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalNovoServico');
    const inputs = modal.querySelectorAll('input, textarea');

    // Garantir que todos os inputs sejam focalizáveis e editáveis
    inputs.forEach(input => {
      input.style.pointerEvents = 'auto';
      input.style.userSelect = 'auto';
      input.style.cursor = input.type === 'text' || input.tagName === 'TEXTAREA' ? 'text' : 'auto';

      // Remover qualquer bloqueio de eventos
      input.addEventListener('click', function(e) {
        e.stopPropagation();
        this.focus();
      });

      input.addEventListener('focus', function() {
        this.style.borderColor = '#ffc107';
        this.style.boxShadow = '0 0 0 0.2rem rgba(255, 193, 7, 0.25)';
      });

      input.addEventListener('blur', function() {
        this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
        this.style.boxShadow = 'none';
      });
    });

    // Configurar modal sem backdrop
    const btnAbrirModal = document.querySelector('[data-bs-target="#modalNovoServico"]');
    if (btnAbrirModal) {
      btnAbrirModal.addEventListener('click', function(e) {
        e.preventDefault();

        // Criar instância do modal sem backdrop
        const modalInstance = new bootstrap.Modal(modal, {
          backdrop: false,
          keyboard: true
        });

        modalInstance.show();

        // Focar no primeiro input após abrir o modal
        setTimeout(() => {
          const primeiroInput = modal.querySelector('#nomeServico');
          if (primeiroInput) {
            primeiroInput.focus();
          }
        }, 300);
      });
    }

    // Fechar modal ao clicar fora dele (já que não temos backdrop)
    document.addEventListener('click', function(e) {
      if (modal.classList.contains('show')) {
        const modalDialog = modal.querySelector('.modal-dialog');
        if (!modalDialog.contains(e.target)) {
          const modalInstance = bootstrap.Modal.getInstance(modal);
          if (modalInstance) {
            modalInstance.hide();
          }
        }
      }
    });

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.classList.contains('show')) {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
          modalInstance.hide();
        }
      }
    });

    // Log para debug
    console.log('Modal de serviços (sem backdrop) configurado corretamente');
    console.log('Inputs encontrados:', inputs.length);
  });
</script>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>