<?php

/**
 * Controller do cliente
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Agendamento.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../models/HorarioDisponivel.php';
require_once __DIR__ . '/../models/Barbeiro.php';

class ClienteController extends BaseController
{

  public function __construct()
  {
    $this->requireAuth('cliente');
  }

  /**
   * Dashboard do cliente
   */
  public function dashboard()
  {
    $agendamentoModel = new Agendamento();
    $proximosAgendamentos = $agendamentoModel->findUpcomingByCliente($_SESSION['user_id']);

    $this->loadView('cliente/dashboard', [
      'proximosAgendamentos' => $proximosAgendamentos
    ]);
  }

  /**
   * Listar agendamentos do cliente
   */
  public function agendamentos()
  {
    $agendamentoModel = new Agendamento();
    $agendamentos = $agendamentoModel->findByCliente($_SESSION['user_id']);

    $this->loadView('cliente/agendamentos', [
      'agendamentos' => $agendamentos
    ]);
  }

  /**
   * Novo agendamento
   */
  public function novoAgendamento()
  {
    $servicoModel = new Servico();
    $barbeiroModel = new Barbeiro();
    $horarioModel = new HorarioDisponivel();

    // Buscar todos barbeiros ativos
    $barbeiros = $barbeiroModel->getBarbeirosAtivos();
    // Buscar todos os serviços disponíveis (com dados do barbeiro)
    $servicos = $servicoModel->findAllWithBarbeiros();
    // Buscar todos os horários disponíveis futuros
    $horarios = $horarioModel->findAvailableForBooking();

    if ($this->isPost()) {
      $barbeiroId = $this->getPost('barbeiro_id');
      $servicoId = $this->getPost('servico_id');
      $horarioId = $this->getPost('horario_id');
      $observacoes = $this->getPost('observacoes');

      $errors = $this->validateRequired([
        'barbeiro_id' => 'Barbeiro',
        'servico_id' => 'Serviço',
        'horario_id' => 'Horário'
      ], $_POST);

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('cliente/novo_agendamento', [
          'barbeiros' => $barbeiros,
          'servicos' => $servicos,
          'horarios' => $horarios
        ]);
        return;
      }

      $horario = $horarioModel->findById($horarioId);
      if (!$horario) {
        $this->setError('Horário não encontrado.');
        $this->loadView('cliente/novo_agendamento', [
          'barbeiros' => $barbeiros,
          'servicos' => $servicos,
          'horarios' => $horarios
        ]);
        return;
      }

      $servico = $servicoModel->findById($servicoId);
      if (!$servico || $servico['barbeiro_id'] != $barbeiroId) {
        $this->setError('Serviço não compatível com o barbeiro selecionado.');
        $this->loadView('cliente/novo_agendamento', [
          'barbeiros' => $barbeiros,
          'servicos' => $servicos,
          'horarios' => $horarios
        ]);
        return;
      }

      $agendamentoModel = new Agendamento();
      if ($agendamentoModel->createAgendamento(
        $_SESSION['user_id'],
        $barbeiroId,
        $servicoId,
        $horario['data_hora'],
        $observacoes
      )) {
        $this->setSuccess('Agendamento realizado com sucesso!');
        redirect('/cliente/agendamentos.php');
      } else {
        $this->setError('Horário não está mais disponível.');
      }
    }

    // GET: Monta tela inicial com barbeiros
    $this->loadView('cliente/novo_agendamento', [
      'barbeiros' => $barbeiros,
      'servicos' => $servicos,
      'horarios' => $horarios
    ]);
  }

  /**
   * Buscar horários disponíveis via AJAX (simulado com GET)
   */
  public function buscarHorarios()
  {
    $servicoId = $this->getGet('servico_id');

    if (!$servicoId) {
      echo json_encode([]);
      return;
    }

    $horarioModel = new HorarioDisponivel();
    $horarios = $horarioModel->findAvailableForBooking($servicoId);

    // Formatar horários para exibição
    $horariosFormatados = [];
    foreach ($horarios as $horario) {
      $horariosFormatados[] = [
        'id' => $horario['id'],
        'data_hora' => $horario['data_hora'],
        'data_hora_formatada' => formatDateBR($horario['data_hora']),
        'barbeiro_nome' => $horario['barbeiro_nome']
      ];
    }

    header('Content-Type: application/json');
    echo json_encode($horariosFormatados);
  }

  /**
   * Cancelar agendamento
   */
  public function cancelarAgendamento()
  {
    $agendamentoId = $this->getGet('id');

    if (!$agendamentoId) {
      $this->setError('Agendamento não encontrado.');
      redirect('cliente/agendamentos.php');
    }

    $agendamentoModel = new Agendamento();

    // Verificar se agendamento pertence ao cliente
    if (!$agendamentoModel->belongsToCliente($agendamentoId, $_SESSION['user_id'])) {
      $this->setError('Você não pode cancelar este agendamento.');
      redirect('cliente/agendamentos.php');
    }

    // Buscar agendamento
    $agendamento = $agendamentoModel->findById($agendamentoId);
    if (!$agendamento) {
      $this->setError('Agendamento não encontrado.');
      redirect('cliente/agendamentos.php');
    }

    // Verificar se agendamento pode ser cancelado (pelo menos 2 horas antes)
    $dataAgendamento = new DateTime($agendamento['data_hora']);
    $agora = new DateTime();
    $agora->add(new DateInterval('PT2H')); // Adicionar 2 horas

    if ($dataAgendamento <= $agora) {
      $this->setError('Agendamentos só podem ser cancelados com pelo menos 2 horas de antecedência.');
      redirect('cliente/agendamentos.php');
    }

    if ($this->isPost()) {
      if ($agendamentoModel->updateStatus($agendamentoId, 'cancelado')) {
        $this->setSuccess('Agendamento cancelado com sucesso.');
      } else {
        $this->setError('Erro ao cancelar agendamento.');
      }
      redirect('cliente/agendamentos.php');
    }

    $agendamento = $agendamentoModel->findCompleteById($agendamentoId);
    $this->loadView('cliente/cancelar_agendamento', [
      'agendamento' => $agendamento
    ]);
  }

  /**
   * Perfil do cliente
   */
  public function perfil()
  {
    $clienteModel = new Cliente();
    $cliente = $clienteModel->findById($_SESSION['user_id']);

    if ($this->isPost()) {
      $nome = $this->getPost('nome');
      $telefone = $this->getPost('telefone');
      $email = $this->getPost('email');
      $senha = $this->getPost('senha');
      $confirmar_senha = $this->getPost('confirmar_senha');

      // Validações
      $errors = $this->validateRequired([
        'nome' => 'Nome',
        'telefone' => 'Telefone',
        'email' => 'Email'
      ], $_POST);

      if ($emailError = $this->validateEmail($email)) {
        $errors[] = $emailError;
      }

      // Verificar se email está disponível
      if (!$clienteModel->isEmailAvailable($email, $_SESSION['user_id'])) {
        $errors[] = 'Este email já está sendo usado por outro cliente.';
      }

      // Verificar se telefone está disponível
      if (!$clienteModel->isTelefoneAvailable($telefone, $_SESSION['user_id'])) {
        $errors[] = 'Este telefone já está sendo usado por outro cliente.';
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
        $this->loadView('cliente/perfil', ['cliente' => $cliente]);
        return;
      }

      // Atualizar cliente
      if ($clienteModel->updateCliente($_SESSION['user_id'], $nome, $telefone, $email, $senha ?: null)) {
        $_SESSION['user_name'] = $nome;
        $this->setSuccess('Perfil atualizado com sucesso!');
        redirect('cliente/perfil.php');
      } else {
        $this->setError('Erro ao atualizar perfil.');
      }
    }

    $this->loadView('cliente/perfil', [
      'cliente' => $cliente
    ]);
  }
}
