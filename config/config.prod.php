<?php

/**
 * Configuração do banco de dados - PRODUÇÃO
 * Sistema de Agendamento - Barbearia
 *
 * Este arquivo é usado em produção com variáveis de ambiente
 */

// Configurações do banco de dados usando variáveis de ambiente
define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'barbearia_agendamento');
define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'barbearia_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('BASE_URL', '/');
define('ROOT_PATH', dirname(__DIR__));

// Detectar ambiente
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? getenv('ENVIRONMENT') ?? 'development');

// Configurações de sessão para produção
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);
ini_set('session.entropy_length', 32);
ini_set('session.entropy_file', '/dev/urandom');

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            // Em produção, não expor detalhes do erro
            if (ENVIRONMENT === 'production') {
                error_log("Database connection error: " . $e->getMessage());
                die("Erro de conexão com o banco de dados. Tente novamente mais tarde.");
            } else {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
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

    // Verificar se a conexão está ativa
    public function isConnected()
    {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Destructor
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

// Configurações de erro baseadas no ambiente
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Headers de segurança
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');

    if (ENVIRONMENT === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header('Content-Security-Policy: default-src \'self\'; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com https://cdn.jsdelivr.net; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; img-src \'self\' data: https:;');
    }
}

// Funções auxiliares
function redirect($path)
{
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function isLoggedIn($type = null)
{
    if ($type === 'cliente') {
        return isset($_SESSION['cliente_id']);
    } elseif ($type === 'barbeiro') {
        return isset($_SESSION['barbeiro_id']);
    }
    return isset($_SESSION['cliente_id']) || isset($_SESSION['barbeiro_id']);
}

function requireLogin($type = null)
{
    if (!isLoggedIn($type)) {
        if ($type === 'cliente') {
            redirect('/auth/cliente_login.php');
        } elseif ($type === 'barbeiro') {
            redirect('/auth/barbeiro_login.php');
        } else {
            redirect('/');
        }
    }
}

function initApp()
{
    // Iniciar sessão se ainda não foi iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Incluir classes base
    require_once ROOT_PATH . '/models/BaseModel.php';
    require_once ROOT_PATH . '/controllers/BaseController.php';

    // Autoload das classes
    spl_autoload_register(function ($class) {
        $directories = [
            ROOT_PATH . '/models/',
            ROOT_PATH . '/controllers/'
        ];

        foreach ($directories as $dir) {
            $file = $dir . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });
}

// Verificar conexão com banco na inicialização (apenas em desenvolvimento)
if (ENVIRONMENT === 'development') {
    try {
        $db = Database::getInstance();
        if (!$db->isConnected()) {
            die("Não foi possível conectar ao banco de dados.");
        }
    } catch (Exception $e) {
        die("Erro ao verificar conexão: " . $e->getMessage());
    }
}
