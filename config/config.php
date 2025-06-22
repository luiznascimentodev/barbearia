<?php

/**
 * Configuração do banco de dados
 * Sistema de Agendamento - Barbearia
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'u898015508_barbearia');
define('DB_USER', 'u898015508_luiz');
define('DB_PASS', 'Felippe@97');
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
//define('BASE_URL', '/');
define('BASE_URL', '/barbearia/public/');
//define('BASE_URL', 'http://localhost/barbearia/public/');
define('ROOT_PATH', dirname(__DIR__));

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 se usar HTTPS

class Database
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    try {
      $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
      $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
      ]);
    } catch (PDOException $e) {
      die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }
}

/**
 * Autoloader simples para carregar modelos
 */
spl_autoload_register(function ($class_name) {
  $file = ROOT_PATH . '/models/' . $class_name . '.php';
  if (file_exists($file)) {
    require_once $file;
  }
});

/**
 * Função para inicializar a aplicação
 */
function initApp()
{
  // Inicializar sessão se não estiver ativa
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  // Definir timezone
  date_default_timezone_set('America/Sao_Paulo');
}

/**
 * Função para verificar se usuário está logado
 */
function isLoggedIn($userType)
{
  return isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === $userType;
}

/**
 * Função para redirecionar usuário
 */
function redirect($path)
{
  $url = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
  header("Location: $url");
  exit();
}

/**
 * Função para limpar dados de entrada
 */
function sanitize($data)
{
  return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Função para validar email
 */
function isValidEmail($email)
{
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Função para gerar hash de senha
 */
function hashPassword($password)
{
  return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Função para verificar senha
 */
function verifyPassword($password, $hash)
{
  return password_verify($password, $hash);
}

/**
 * Função para formatar data brasileira
 */
function formatDateBR($date)
{
  return date('d/m/Y H:i', strtotime($date));
}

/**
 * Função para formatar preço
 */
function formatPrice($price)
{
  return 'R$ ' . number_format($price, 2, ',', '.');
}

/**
 * Proteger rotas de cliente
 */
function checkClientAuth()
{
  if (!isset($_SESSION['cliente_id'])) {
    redirect('/auth/cliente_login.php');
  }
}

/**
 * Proteger rotas de barbeiro
 */
function checkBarberAuth()
{
  if (!isset($_SESSION['barbeiro_id'])) {
    redirect('/auth/barbeiro_login.php');
  }
}

/**
 * Função para setar mensagens flash na sessão
 */
function setFlashMessage($message, $type = 'success')
{
  if (!isset($_SESSION)) session_start();
  $_SESSION[$type] = $message;
}

/**
 * Função utilitária para construir query string preservando parâmetros
 * Exemplo: buildQueryString(['page' => 2])
 */
function buildQueryString($params = [])
{
  $query = array_merge($_GET, $params);
  return http_build_query($query);
}

/**
 * Formata data/hora para d/m/Y H:i
 */
function formatDateTime($dateTime)
{
  if (!$dateTime) return '';
  return date('d/m/Y H:i', strtotime($dateTime));
}
