<?php

/**
 * Controller do barbeiro
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Barbeiro.php';
require_once __DIR__ . '/../models/Agendamento.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../models/HorarioDisponivel.php';
require_once __DIR__ . '/../models/Cliente.php';

class BarbeiroController extends BaseController
{

  public function __construct()
  {
    $this->requireAuth('barbeiro');
  }

  /**
   * Dashboard do barbeiro
   */
  public function dashboard()
  {
    $agendamentoModel = new Agendamento();
    $barbeiroModel = new Barbeiro();

    $proximosAgendamentos = $agendamentoModel->findUpcomingByBarbeiro($_SESSION['user_id']);
    $estatisticas = $barbeiroModel->getStatistics($_SESSION['user_id']);

    $this->loadView('barbeiro/dashboard', [
      'proximosAgendamentos' => $proximosAgendamentos,
      'estatisticas' => $estatisticas
    ]);
  }

  /**
   * Listar agendamentos do barbeiro
   */
  public function agendamentos()
  {
    $agendamentoModel = new Agendamento();
    $agendamentos = $agendamentoModel->findByBarbeiro($_SESSION['user_id']);

    $this->loadView('barbeiro/agendamentos', [
      'agendamentos' => $agendamentos
    ]);
  }

  /**
   * Confirmar agendamento
   */
  public function confirmarAgendamento()
  {
    $agendamentoId = $this->getGet('id');

    if (!$agendamentoId) {
      $this->setError('Agendamento não encontrado.');
      redirect('barbeiro/agendamentos.php');
    }

    $agendamentoModel = new Agendamento();

    // Verificar se agendamento pertence ao barbeiro
    if (!$agendamentoModel->belongsToBarbeiro($agendamentoId, $_SESSION['user_id'])) {
      $this->setError('Você não pode confirmar este agendamento.');
      redirect('barbeiro/agendamentos.php');
    }

    if ($agendamentoModel->updateStatus($agendamentoId, 'confirmado')) {
      $this->setSuccess('Agendamento confirmado com sucesso.');
    } else {
      $this->setError('Erro ao confirmar agendamento.');
    }

    redirect('barbeiro/agendamentos.php');
  }

  /**
   * Cancelar agendamento
   */
  public function cancelarAgendamento()
  {
    $agendamentoId = $this->getGet('id');

    if (!$agendamentoId) {
      $this->setError('Agendamento não encontrado.');
      redirect('barbeiro/agendamentos.php');
    }

    $agendamentoModel = new Agendamento();

    // Verificar se agendamento pertence ao barbeiro
    if (!$agendamentoModel->belongsToBarbeiro($agendamentoId, $_SESSION['user_id'])) {
      $this->setError('Você não pode cancelar este agendamento.');
      redirect('barbeiro/agendamentos.php');
    }

    if ($agendamentoModel->updateStatus($agendamentoId, 'cancelado')) {
      $this->setSuccess('Agendamento cancelado com sucesso.');
    } else {
      $this->setError('Erro ao cancelar agendamento.');
    }

    redirect('barbeiro/agendamentos.php');
  }

  /**
   * Finalizar agendamento
   */
  public function finalizarAgendamento()
  {
    $agendamentoId = $this->getGet('id');

    if (!$agendamentoId) {
      $this->setError('Agendamento não encontrado.');
      redirect('barbeiro/agendamentos.php');
    }

    $agendamentoModel = new Agendamento();

    // Verificar se agendamento pertence ao barbeiro
    if (!$agendamentoModel->belongsToBarbeiro($agendamentoId, $_SESSION['user_id'])) {
      $this->setError('Você não pode finalizar este agendamento.');
      redirect('barbeiro/agendamentos.php');
    }

    // Atualizar status para finalizado
    if ($agendamentoModel->updateStatus($agendamentoId, 'finalizado')) {
      $this->setSuccess('Agendamento finalizado com sucesso!');
    } else {
      $this->setError('Erro ao finalizar agendamento.');
    }
    redirect('barbeiro/agendamentos.php');
  }

  /**
   * Gerenciar serviços
   */
  public function servicos()
  {
    $servicoModel = new Servico();
    $servicos = $servicoModel->findByBarbeiro($_SESSION['user_id']);

    $this->loadView('barbeiro/servicos', [
      'servicos' => $servicos
    ]);
  }

  /**
   * Novo serviço
   */
  public function novoServico()
  {
    if ($this->isPost()) {
      $nome = $this->getPost('nome');
      $descricao = $this->getPost('descricao');
      $preco = $this->getPost('preco');

      // Validações
      $errors = $this->validateRequired([
        'nome' => 'Nome',
        'preco' => 'Preço'
      ], $_POST);

      if ($precoError = $this->validatePrice($preco)) {
        $errors[] = $precoError;
      }

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('barbeiro/novo_servico');
        return;
      }

      // Criar serviço
      $servicoModel = new Servico();
      if ($servicoModel->createServico($_SESSION['user_id'], $nome, $descricao, $preco)) {
        $this->setSuccess('Serviço criado com sucesso!');
        redirect('barbeiro/servicos.php');
      } else {
        $this->setError('Erro ao criar serviço.');
      }
    }

    $this->loadView('barbeiro/novo_servico');
  }

  /**
   * Editar serviço
   */
  public function editarServico()
  {
    $servicoId = $this->getGet('id');

    if (!$servicoId) {
      $this->setError('Serviço não encontrado.');
      redirect('barbeiro/servicos.php');
    }

    $servicoModel = new Servico();

    // Verificar se serviço pertence ao barbeiro
    if (!$servicoModel->belongsToBarbeiro($servicoId, $_SESSION['user_id'])) {
      $this->setError('Você não pode editar este serviço.');
      redirect('barbeiro/servicos.php');
    }

    $servico = $servicoModel->findById($servicoId);
    if (!$servico) {
      $this->setError('Serviço não encontrado.');
      redirect('barbeiro/servicos.php');
    }

    if ($this->isPost()) {
      $nome = $this->getPost('nome');
      $descricao = $this->getPost('descricao');
      $preco = $this->getPost('preco');

      // Validações
      $errors = $this->validateRequired([
        'nome' => 'Nome',
        'preco' => 'Preço'
      ], $_POST);

      if ($precoError = $this->validatePrice($preco)) {
        $errors[] = $precoError;
      }

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('barbeiro/editar_servico', ['servico' => $servico]);
        return;
      }

      // Atualizar serviço
      if ($servicoModel->updateServico($servicoId, $nome, $descricao, $preco)) {
        $this->setSuccess('Serviço atualizado com sucesso!');
        redirect('barbeiro/servicos.php');
      } else {
        $this->setError('Erro ao atualizar serviço.');
      }
    }

    $this->loadView('barbeiro/editar_servico', [
      'servico' => $servico
    ]);
  }

  /**
   * Excluir serviço
   */
  public function excluirServico()
  {
    $servicoId = $this->getGet('id');

    if (!$servicoId) {
      $this->setError('Serviço não encontrado.');
      redirect('barbeiro/servicos.php');
    }

    $servicoModel = new Servico();

    // Verificar se serviço pertence ao barbeiro
    if (!$servicoModel->belongsToBarbeiro($servicoId, $_SESSION['user_id'])) {
      $this->setError('Você não pode excluir este serviço.');
      redirect('barbeiro/servicos.php');
    }

    // Verificar se serviço tem agendamentos
    if ($servicoModel->hasAgendamentos($servicoId)) {
      $this->setError('Não é possível excluir um serviço que possui agendamentos.');
      redirect('barbeiro/servicos.php');
    }

    if ($servicoModel->delete($servicoId)) {
      $this->setSuccess('Serviço excluído com sucesso.');
    } else {
      $this->setError('Erro ao excluir serviço.');
    }

    redirect('barbeiro/servicos.php');
  }

  /**
   * Gerenciar horários
   */
  public function horarios()
  {
    $horarioModel = new HorarioDisponivel();
    $horarios = $horarioModel->findWithAgendamentoStatus($_SESSION['user_id']);

    $this->loadView('barbeiro/horarios', [
      'horarios' => $horarios
    ]);
  }

  /**
   * Novo horário
   */
  public function novoHorario()
  {
    if ($this->isPost()) {
      $data = $this->getPost('data');
      $hora = $this->getPost('hora');

      // Validações
      $errors = $this->validateRequired([
        'data' => 'Data',
        'hora' => 'Hora'
      ], $_POST);

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('barbeiro/novo_horario');
        return;
      }

      $dataHora = $data . ' ' . $hora . ':00';

      if ($datetimeError = $this->validateDateTime($dataHora)) {
        $this->setError($datetimeError);
        $this->loadView('barbeiro/novo_horario');
        return;
      }

      // Criar horário
      $horarioModel = new HorarioDisponivel();
      if ($horarioModel->createHorario($_SESSION['user_id'], $dataHora)) {
        $this->setSuccess('Horário criado com sucesso!');
        redirect('barbeiro/horarios.php');
      } else {
        $this->setError('Este horário já existe.');
      }
    }

    $this->loadView('barbeiro/novo_horario');
  }

  /**
   * Excluir horário
   */
  public function excluirHorario()
  {
    $horarioId = $this->getGet('id');

    if (!$horarioId) {
      $this->setError('Horário não encontrado.');
      redirect('barbeiro/horarios.php');
    }

    $horarioModel = new HorarioDisponivel();

    // Verificar se horário pertence ao barbeiro
    if (!$horarioModel->belongsToBarbeiro($horarioId, $_SESSION['user_id'])) {
      $this->setError('Você não pode excluir este horário.');
      redirect('barbeiro/horarios.php');
    }

    if ($horarioModel->delete($horarioId)) {
      $this->setSuccess('Horário excluído com sucesso.');
    } else {
      $this->setError('Erro ao excluir horário.');
    }

    redirect('barbeiro/horarios.php');
  }

  /**
   * Clientes do barbeiro
   */
  public function clientes()
  {
    $clienteModel = new Cliente();
    $clientes = $clienteModel->getClientesByBarbeiro($_SESSION['user_id']);

    $this->loadView('barbeiro/clientes', [
      'clientes' => $clientes
    ]);
  }

  /**
   * Perfil do barbeiro
   */
  public function perfil()
  {
    $barbeiroModel = new Barbeiro();
    $barbeiro = $barbeiroModel->findById($_SESSION['user_id']);

    if ($this->isPost()) {
      $nome = $this->getPost('nome');
      $email = $this->getPost('email');
      $senha = $this->getPost('senha');
      $confirmar_senha = $this->getPost('confirmar_senha');

      // Validações
      $errors = $this->validateRequired([
        'nome' => 'Nome',
        'email' => 'Email'
      ], $_POST);

      if ($emailError = $this->validateEmail($email)) {
        $errors[] = $emailError;
      }

      // Verificar se email está disponível
      if (!$barbeiroModel->isEmailAvailable($email, $_SESSION['user_id'])) {
        $errors[] = 'Este email já está sendo usado por outro barbeiro.';
      }

      // Validar senha se informada
      if (!empty($senha)) {
        if ($senhaError = $this->validatePassword($senha)) {
          $errors[] = $senhaError;
        }

        if ($senha !== $confirmar_senha) {
          $errors[] = 'As senhas não coincidem.';
        }
      }

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('barbeiro/perfil', ['barbeiro' => $barbeiro]);
        return;
      }

      // Atualizar barbeiro
      if ($barbeiroModel->updateBarbeiro($_SESSION['user_id'], $nome, $email, $senha ?: null)) {
        $_SESSION['user_name'] = $nome;
        $this->setSuccess('Perfil atualizado com sucesso!');
        redirect('barbeiro/perfil.php');
      } else {
        $this->setError('Erro ao atualizar perfil.');
      }
    }

    $this->loadView('barbeiro/perfil', [
      'barbeiro' => $barbeiro
    ]);
  }
}
