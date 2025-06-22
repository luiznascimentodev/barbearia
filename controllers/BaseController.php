<?php

/**
 * Controller base para todos os outros controllers
 */

require_once __DIR__ . '/../config/config.php';

abstract class BaseController
{
  protected $data = [];

  /**
   * Carregar view
   */
  protected function loadView($view, $data = [])
  {
    $this->data = array_merge($this->data, $data);
    extract($this->data);

    // Procura apenas em views/ (NÃO mais em public/)
    $viewsPath = __DIR__ . '/../views/' . $view . '.php';
    if (file_exists($viewsPath)) {
      include $viewsPath;
    } else {
      echo '<div style="color:red">View não encontrada: ' . htmlspecialchars($view) . '</div>';
    }
  }

  /**
   * Definir mensagem de sucesso
   */
  protected function setSuccess($message)
  {
    $_SESSION['success'] = $message;
  }

  /**
   * Definir mensagem de erro
   */
  protected function setError($message)
  {
    $_SESSION['error'] = $message;
  }

  /**
   * Verificar se é requisição POST
   */
  protected function isPost()
  {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
  }

  /**
   * Obter dados do POST
   */
  protected function getPost($key = null, $default = null)
  {
    if ($key === null) {
      return $_POST;
    }

    return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
  }

  /**
   * Obter dados do GET
   */
  protected function getGet($key = null, $default = null)
  {
    if ($key === null) {
      return $_GET;
    }

    return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
  }

  /**
   * Verificar se usuário está logado
   */
  protected function requireAuth($userType)
  {
    if (!isLoggedIn($userType)) {
      $this->setError('Você precisa estar logado para acessar esta área.');
      redirect("auth/{$userType}_login.php");
    }
  }

  /**
   * Validar campos obrigatórios
   */
  protected function validateRequired($fields, $data)
  {
    $errors = [];

    foreach ($fields as $field => $label) {
      if (empty($data[$field])) {
        $errors[] = "O campo {$label} é obrigatório.";
      }
    }

    return $errors;
  }

  /**
   * Validar email
   */
  protected function validateEmail($email)
  {
    if (!isValidEmail($email)) {
      return 'Email inválido.';
    }
    return null;
  }

  /**
   * Validar senha
   */
  protected function validatePassword($password)
  {
    if (strlen($password) < 6) {
      return 'A senha deve ter pelo menos 6 caracteres.';
    }
    return null;
  }

  /**
   * Validar preço
   */
  protected function validatePrice($price)
  {
    if (!is_numeric($price) || $price <= 0) {
      return 'Preço deve ser um valor numérico positivo.';
    }
    return null;
  }

  /**
   * Validar data e hora
   */
  protected function validateDateTime($datetime)
  {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    if (!$date || $date->format('Y-m-d H:i:s') !== $datetime) {
      return 'Data e hora inválidas.';
    }

    if ($date <= new DateTime()) {
      return 'A data e hora devem ser futuras.';
    }

    return null;
  }

  /**
   * Redirecionar com mensagem
   */
  protected function redirectWithMessage($path, $message, $type = 'success')
  {
    if ($type === 'success') {
      $this->setSuccess($message);
    } else {
      $this->setError($message);
    }
    redirect($path);
  }
}
