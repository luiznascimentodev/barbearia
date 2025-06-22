<?php

/**
 * Model para gerenciamento de clientes
 */

require_once __DIR__ . '/BaseModel.php';

class Cliente extends BaseModel
{
  protected $table = 'clientes';

  /**
   * Buscar cliente por email
   */
  public function findByEmail($email)
  {
    return $this->findBy('email', $email);
  }

  /**
   * Buscar cliente por telefone
   */
  public function findByTelefone($telefone)
  {
    return $this->findBy('telefone', $telefone);
  }

  /**
   * Criar novo cliente
   */
  public function createCliente($nome, $telefone, $email, $senha)
  {
    // Verificar se email já existe
    if ($this->findByEmail($email)) {
      return false;
    }

    // Verificar se telefone já existe
    if ($this->findByTelefone($telefone)) {
      return false;
    }

    $data = [
      'nome' => $nome,
      'telefone' => $telefone,
      'email' => $email,
      'senha_hash' => hashPassword($senha)
    ];

    return $this->create($data);
  }

  /**
   * Autenticar cliente
   */
  public function authenticate($email, $senha)
  {
    $cliente = $this->findByEmail($email);

    if ($cliente && verifyPassword($senha, $cliente['senha_hash'])) {
      return $cliente;
    }

    return false;
  }

  /**
   * Atualizar dados do cliente
   */
  public function updateCliente($id, $nome, $telefone, $email, $senha = null)
  {
    $data = [
      'nome' => $nome,
      'telefone' => $telefone,
      'email' => $email
    ];

    if ($senha) {
      $data['senha_hash'] = hashPassword($senha);
    }

    return $this->update($id, $data);
  }

  /**
   * Verificar se email está disponível (para edição)
   */
  public function isEmailAvailable($email, $excludeId = null)
  {
    $sql = "SELECT id FROM {$this->table} WHERE email = ?";
    $params = [$email];

    if ($excludeId) {
      $sql .= " AND id != ?";
      $params[] = $excludeId;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetch() === false;
  }

  /**
   * Verificar se telefone está disponível (para edição)
   */
  public function isTelefoneAvailable($telefone, $excludeId = null)
  {
    $sql = "SELECT id FROM {$this->table} WHERE telefone = ?";
    $params = [$telefone];

    if ($excludeId) {
      $sql .= " AND id != ?";
      $params[] = $excludeId;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetch() === false;
  }

  /**
   * Obter clientes que agendaram com um barbeiro específico
   */
  public function getClientesByBarbeiro($barbeiroId, $busca = '', $offset = 0, $limit = 20)
  {
    $sql = "
            SELECT DISTINCT c.id, c.nome, c.telefone, c.email, c.created_at as data_cadastro,
                   COUNT(a.id) as total_agendamentos,
                   COUNT(CASE WHEN a.status = 'finalizado' THEN 1 END) as agendamentos_finalizados,
                   MAX(a.data_hora) as ultimo_agendamento
            FROM clientes c
            INNER JOIN agendamentos a ON c.id = a.cliente_id
            WHERE a.barbeiro_id = ?
        ";

    $params = [$barbeiroId];

    if (!empty($busca)) {
      $sql .= " AND (c.nome LIKE ? OR c.email LIKE ? OR c.telefone LIKE ?)";
      $buscaParam = '%' . $busca . '%';
      $params[] = $buscaParam;
      $params[] = $buscaParam;
      $params[] = $buscaParam;
    }

    $sql .= " GROUP BY c.id ORDER BY c.nome LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
  }

  /**
   * Contar clientes únicos que já agendaram com um barbeiro
   */
  public function countClientesByBarbeiro($barbeiroId)
  {
    $sql = "SELECT COUNT(DISTINCT cliente_id) as total FROM agendamentos WHERE barbeiro_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);
    $result = $stmt->fetch();
    return $result ? (int)$result['total'] : 0;
  }
}
