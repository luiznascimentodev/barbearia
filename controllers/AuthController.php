<?php

/**
 * Controller de autenticação
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Barbeiro.php';

class AuthController extends BaseController
{

  /**
   * Login do cliente
   */
  public function clienteLogin()
  {
    if ($this->isPost()) {
      $email = $this->getPost('email');
      $senha = $this->getPost('senha');

      // Validações
      $errors = $this->validateRequired([
        'email' => 'Email',
        'senha' => 'Senha'
      ], $_POST);

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('auth/cliente_login');
        return;
      }

      // Autenticar cliente
      $clienteModel = new Cliente();
      $cliente = $clienteModel->authenticate($email, $senha);

      if ($cliente) {
        $_SESSION['user_id'] = $cliente['id'];
        $_SESSION['user_type'] = 'cliente';
        $_SESSION['user_name'] = $cliente['nome'];
        $_SESSION['cliente_id'] = $cliente['id'];

        $this->setSuccess('Login realizado com sucesso!');
        redirect('cliente/dashboard.php');
      } else {
        $this->setError('Email ou senha incorretos.');
      }
    }

    $this->loadView('auth/cliente_login');
  }

  /**
   * Registro do cliente
   */
  public function clienteRegister()
  {
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
        'email' => 'Email',
        'senha' => 'Senha',
        'confirmar_senha' => 'Confirmar senha'
      ], $_POST);

      if ($emailError = $this->validateEmail($email)) {
        $errors[] = $emailError;
      }

      if ($senhaError = $this->validatePassword($senha)) {
        $errors[] = $senhaError;
      }

      if ($senha !== $confirmar_senha) {
        $errors[] = 'As senhas não coincidem.';
      }

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('auth/cliente_register');
        return;
      }

      // Criar cliente
      $clienteModel = new Cliente();
      if ($clienteModel->createCliente($nome, $telefone, $email, $senha)) {
        $this->setSuccess('Cadastro realizado com sucesso! Faça seu login.');
        redirect(BASE_URL . '/auth/cliente_login.php');
      } else {
        $this->setError('Email ou telefone já cadastrados.');
      }
    }

    $this->loadView('auth/cliente_register');
  }

  /**
   * Login do barbeiro
   */
  public function barbeiroLogin()
  {
    if ($this->isPost()) {
      $email = $this->getPost('email');
      $senha = $this->getPost('senha');

      // Validações
      $errors = $this->validateRequired([
        'email' => 'Email',
        'senha' => 'Senha'
      ], $_POST);

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('auth/barbeiro_login');
        return;
      }

      // Autenticar barbeiro
      $barbeiroModel = new Barbeiro();
      $barbeiro = $barbeiroModel->authenticate($email, $senha);

      if ($barbeiro) {
        $_SESSION['user_id'] = $barbeiro['id'];
        $_SESSION['user_type'] = 'barbeiro';
        $_SESSION['user_name'] = $barbeiro['nome'];
        $_SESSION['barbeiro_id'] = $barbeiro['id'];

        $this->setSuccess('Login realizado com sucesso!');
        redirect('barbeiro/dashboard.php');
      } else {
        $this->setError('Email ou senha incorretos.');
      }
    }

    $this->loadView('auth/barbeiro_login');
  }

  /**
   * Registro do barbeiro
   */
  public function barbeiroRegister()
  {
    if ($this->isPost()) {
      $nome = $this->getPost('nome');
      $email = $this->getPost('email');
      $senha = $this->getPost('senha');
      $confirmar_senha = $this->getPost('confirmar_senha');

      // Validações
      $errors = $this->validateRequired([
        'nome' => 'Nome',
        'email' => 'Email',
        'senha' => 'Senha',
        'confirmar_senha' => 'Confirmar senha'
      ], $_POST);

      if ($emailError = $this->validateEmail($email)) {
        $errors[] = $emailError;
      }

      if ($senhaError = $this->validatePassword($senha)) {
        $errors[] = $senhaError;
      }

      if ($senha !== $confirmar_senha) {
        $errors[] = 'As senhas não coincidem.';
      }

      if (!empty($errors)) {
        $this->setError(implode('<br>', $errors));
        $this->loadView('auth/barbeiro_register');
        return;
      }

      // Criar barbeiro
      $barbeiroModel = new Barbeiro();
      if ($barbeiroModel->createBarbeiro($nome, $email, $senha)) {
        $this->setSuccess('Cadastro realizado com sucesso! Faça seu login.');
        redirect(BASE_URL . 'auth/barbeiro_login.php');
      } else {
        $this->setError('Email já cadastrado.');
      }
    }

    $this->loadView('auth/barbeiro_register');
  }

  /**
   * Logout
   */
  public function logout()
  {
    session_destroy();
    redirect('/');
  }
}
