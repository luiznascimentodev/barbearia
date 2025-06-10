<?php

/**
 * Configuração do banco de dados - EXEMPLO
 * Sistema de Agendamento - Barbearia
 *
 * INSTRUÇÕES:
 * 1. Copie este arquivo para config.php
 * 2. Altere as configurações abaixo com seus dados
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'barbearia_agendamento');
define('DB_USER', 'seu_usuario_aqui');
define('DB_PASS', 'sua_senha_aqui');
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('BASE_URL', '/');
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

  // Método para executar queries SELECT
  public function select($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);
      return $stmt->fetchAll();
    } catch (PDOException $e) {
      error_log("Erro na query SELECT: " . $e->getMessage());
      return false;
    }
  }

  // Método para executar queries INSERT, UPDATE, DELETE
  public function execute($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      return $stmt->execute($params);
    } catch (PDOException $e) {
      error_log("Erro na query: " . $e->getMessage());
      return false;
    }
  }

  // Método para obter o último ID inserido
  public function lastInsertId()
  {
    return $this->connection->lastInsertId();
  }

  // Método para contar registros
  public function count($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);
      return $stmt->rowCount();
    } catch (PDOException $e) {
      error_log("Erro na contagem: " . $e->getMessage());
      return 0;
    }
  }

  // Método para verificar se um registro existe
  public function exists($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);
      return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
      error_log("Erro na verificação: " . $e->getMessage());
      return false;
    }
  }

  // Método para iniciar transação
  public function beginTransaction()
  {
    return $this->connection->beginTransaction();
  }

  // Método para confirmar transação
  public function commit()
  {
    return $this->connection->commit();
  }

  // Método para desfazer transação
  public function rollback()
  {
    return $this->connection->rollback();
  }

  // Método para escape manual (use PDO prepared statements sempre que possível)
  public function quote($string)
  {
    return $this->connection->quote($string);
  }

  // Método para obter informações da conexão
  public function getInfo()
  {
    return [
      'host' => DB_HOST,
      'database' => DB_NAME,
      'charset' => DB_CHARSET,
      'driver' => $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME),
      'version' => $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION)
    ];
  }

  // Método para verificar se a conexão está ativa
  public function isConnected()
  {
    try {
      $this->connection->query('SELECT 1');
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  // Método para fechar a conexão
  public function close()
  {
    $this->connection = null;
    self::$instance = null;
  }

  // Método para obter uma única linha
  public function selectOne($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);
      return $stmt->fetch();
    } catch (PDOException $e) {
      error_log("Erro na query SELECT ONE: " . $e->getMessage());
      return false;
    }
  }

  // Método para inserir e retornar o ID
  public function insertAndGetId($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      if ($stmt->execute($params)) {
        return $this->connection->lastInsertId();
      }
      return false;
    } catch (PDOException $e) {
      error_log("Erro no INSERT: " . $e->getMessage());
      return false;
    }
  }

  // Destructor para garantir que a conexão seja fechada
  public function __destruct()
  {
    $this->connection = null;
  }
}

// Função auxiliar para obter a instância do banco
function getDatabase()
{
  return Database::getInstance();
}

// Função auxiliar para obter a conexão PDO direta
function getConnection()
{
  return Database::getInstance()->getConnection();
}

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de erro (para desenvolvimento)
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
} else {
  error_reporting(0);
  ini_set('display_errors', 0);
}

// Configurações de segurança
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.entropy_length', 32);
ini_set('session.entropy_file', '/dev/urandom');

// Headers de segurança
if (!headers_sent()) {
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: DENY');
  header('X-XSS-Protection: 1; mode=block');
}
