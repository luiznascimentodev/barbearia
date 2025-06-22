<?php

/**
 * Model para gerenciamento de barbeiros
 */

require_once __DIR__ . '/BaseModel.php';

class Barbeiro extends BaseModel
{
  protected $table = 'barbeiros';

  /**
   * Buscar barbeiro por email
   */
  public function findByEmail($email)
  {
    return $this->findBy('email', $email);
  }

  /**
   * Criar novo barbeiro
   */
  public function createBarbeiro($nome, $email, $senha)
  {
    // Verificar se email já existe
    if ($this->findByEmail($email)) {
      return false;
    }

    $data = [
      'nome' => $nome,
      'email' => $email,
      'senha_hash' => hashPassword($senha)
    ];

    return $this->create($data);
  }

  /**
   * Autenticar barbeiro
   */
  public function authenticate($email, $senha)
  {
    $barbeiro = $this->findByEmail($email);

    if ($barbeiro && verifyPassword($senha, $barbeiro['senha_hash'])) {
      return $barbeiro;
    }

    return false;
  }

  /**
   * Atualizar dados do barbeiro
   */
  public function updateBarbeiro($id, $nome, $email, $senha = null)
  {
    $data = [
      'nome' => $nome,
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
   * Obter estatísticas do barbeiro
   */
  public function getStatistics($barbeiroId)
  {
    $sql = "
            SELECT
                COUNT(a.id) as total_agendamentos,
                COUNT(CASE WHEN a.status = 'confirmado' THEN 1 END) as confirmados,
                COUNT(CASE WHEN a.status = 'pendente' THEN 1 END) as pendentes,
                COUNT(CASE WHEN a.status = 'cancelado' THEN 1 END) as cancelados
            FROM agendamentos a
            WHERE a.barbeiro_id = ?
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);

    return $stmt->fetch();
  }

  /**
   * Buscar barbeiros ativos para exibição (remove campo especialidade se não existir)
   */
  public function getBarbeirosAtivos($limit = null)
  {
    $sql = "
            SELECT
                b.*,
                COUNT(DISTINCT s.id) as total_servicos,
                COUNT(DISTINCT a.id) as total_agendamentos
            FROM {$this->table} b
            LEFT JOIN servicos s ON s.barbeiro_id = b.id
            LEFT JOIN agendamentos a ON a.barbeiro_id = b.id AND a.status = 'finalizado'
            GROUP BY b.id
            ORDER BY total_agendamentos DESC, b.nome ASC
        ";

    if ($limit) {
      $sql .= " LIMIT " . intval($limit);
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
  }
}
