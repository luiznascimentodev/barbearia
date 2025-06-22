<?php

/**
 * Model para gerenciamento de agendamentos
 */

require_once __DIR__ . '/BaseModel.php';

class Agendamento extends BaseModel
{
  protected $table = 'agendamentos';

  /**
   * Buscar agendamentos por cliente
   */
  public function findByCliente($clienteId)
  {
    $sql = "
            SELECT a.*,
                   s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
                   b.nome as barbeiro_nome, b.email as barbeiro_email,
                   DATE(a.data_hora) as data_agendamento,
                   TIME(a.data_hora) as horario
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN barbeiros b ON a.barbeiro_id = b.id
            WHERE a.cliente_id = ?
            ORDER BY a.data_hora DESC
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$clienteId]);

    return $stmt->fetchAll();
  }

  /**
   * Buscar agendamentos por barbeiro
   */
  public function findByBarbeiro($barbeiroId)
  {
    $sql = "
            SELECT a.*,
                   s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
                   c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email,
                   DATE(a.data_hora) as data_agendamento,
                   TIME(a.data_hora) as horario
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN clientes c ON a.cliente_id = c.id
            WHERE a.barbeiro_id = ?
            ORDER BY a.data_hora DESC
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);

    return $stmt->fetchAll();
  }

  /**
   * Criar novo agendamento
   */
  public function createAgendamento($clienteId, $barbeiroId, $servicoId, $dataHora, $observacoes = null)
  {
    // Verificar se horário está disponível
    if (!$this->isHorarioDisponivel($barbeiroId, $dataHora)) {
      return false;
    }

    $data = [
      'cliente_id' => $clienteId,
      'barbeiro_id' => $barbeiroId,
      'servico_id' => $servicoId,
      'data_hora' => $dataHora,
      'observacoes' => $observacoes,
      'status' => 'pendente'
    ];

    return $this->create($data);
  }

  /**
   * Verificar se horário está disponível
   */
  public function isHorarioDisponivel($barbeiroId, $dataHora)
  {
    $sql = "
            SELECT COUNT(*) as total
            FROM {$this->table}
            WHERE barbeiro_id = ? AND data_hora = ? AND status != 'cancelado'
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $dataHora]);

    $result = $stmt->fetch();
    return $result['total'] == 0;
  }

  /**
   * Atualizar status do agendamento
   */
  public function updateStatus($id, $status)
  {
    return $this->update($id, ['status' => $status]);
  }

  /**
   * Verificar se agendamento pertence ao cliente
   */
  public function belongsToCliente($agendamentoId, $clienteId)
  {
    $sql = "SELECT id FROM {$this->table} WHERE id = ? AND cliente_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$agendamentoId, $clienteId]);

    return $stmt->fetch() !== false;
  }

  /**
   * Verificar se agendamento pertence ao barbeiro
   */
  public function belongsToBarbeiro($agendamentoId, $barbeiroId)
  {
    $sql = "SELECT id FROM {$this->table} WHERE id = ? AND barbeiro_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$agendamentoId, $barbeiroId]);

    return $stmt->fetch() !== false;
  }

  /**
   * Buscar agendamento completo (com todos os dados relacionados)
   */
  public function findCompleteById($id)
  {
    $sql = "
            SELECT a.*,
                   s.nome as servico_nome, s.preco as servico_preco, s.descricao as servico_descricao,
                   b.nome as barbeiro_nome, b.email as barbeiro_email,
                   c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN barbeiros b ON a.barbeiro_id = b.id
            INNER JOIN clientes c ON a.cliente_id = c.id
            WHERE a.id = ?
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch();
  }

  /**
   * Buscar próximos agendamentos do barbeiro
   */
  public function findUpcomingByBarbeiro($barbeiroId, $limit = 5)
  {
    $sql = "
            SELECT a.*,
                   s.nome as servico_nome, s.duracao as servico_duracao,
                   c.nome as cliente_nome, c.telefone as cliente_telefone,
                   DATE(a.data_hora) as data_agendamento,
                   TIME(a.data_hora) as horario
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN clientes c ON a.cliente_id = c.id
            WHERE a.barbeiro_id = ? AND a.data_hora >= NOW() AND a.status != 'cancelado'
            ORDER BY a.data_hora ASC
            LIMIT ?
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $limit]);

    return $stmt->fetchAll();
  }

  /**
   * Buscar próximos agendamentos do cliente
   */
  public function findUpcomingByCliente($clienteId, $limit = 5)
  {
    $sql = "
            SELECT a.*, s.nome as servico_nome, b.nome as barbeiro_nome
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN barbeiros b ON a.barbeiro_id = b.id
            WHERE a.cliente_id = ? AND a.data_hora >= NOW() AND a.status != 'cancelado'
            ORDER BY a.data_hora ASC
            LIMIT ?
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$clienteId, $limit]);

    return $stmt->fetchAll();
  }

  /**
   * Buscar agendamentos do barbeiro por data específica
   */
  public function getAgendamentosByBarbeiroDate($barbeiroId, $data)
  {
    $sql = "
            SELECT a.*,
                   s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
                   c.nome as cliente_nome, c.telefone as cliente_telefone,
                   DATE(a.data_hora) as data_agendamento,
                   TIME(a.data_hora) as horario
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN clientes c ON a.cliente_id = c.id
            WHERE a.barbeiro_id = ? AND DATE(a.data_hora) = ?
            ORDER BY a.data_hora ASC
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data]);

    return $stmt->fetchAll();
  }

  /**
   * Obter estatísticas do barbeiro por período
   */
  public function getStatsByBarbeiro($barbeiroId, $dataInicio = null, $dataFim = null)
  {
    $sql = "
            SELECT
                COUNT(a.id) as total_agendamentos,
                COUNT(CASE WHEN a.status = 'confirmado' THEN 1 END) as confirmados,
                COUNT(CASE WHEN a.status = 'pendente' THEN 1 END) as pendentes,
                COUNT(CASE WHEN a.status = 'cancelado' THEN 1 END) as cancelados,
                COUNT(CASE WHEN a.status = 'finalizado' THEN 1 END) as finalizados,
                SUM(CASE WHEN a.status = 'finalizado' THEN s.preco ELSE 0 END) as faturamento
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            WHERE a.barbeiro_id = ?
        ";

    $params = [$barbeiroId];

    if ($dataInicio && $dataFim) {
      $sql .= " AND DATE(a.data_hora) BETWEEN ? AND ?";
      $params[] = $dataInicio;
      $params[] = $dataFim;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetch();
  }

  /**
   * Buscar próximos agendamentos (alias para compatibilidade)
   */
  public function getProximosAgendamentos($barbeiroId, $limit = 5)
  {
    return $this->findUpcomingByBarbeiro($barbeiroId, $limit);
  }

  /**
   * Buscar agendamentos com filtros e paginação
   */
  public function getAgendamentosWithDetails($filtros = [], $offset = 0, $limit = 20)
  {
    $sql = "
            SELECT a.*,
                   s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
                   c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email,
                   b.nome as barbeiro_nome,
                   DATE(a.data_hora) as data_agendamento,
                   TIME(a.data_hora) as horario
            FROM {$this->table} a
            INNER JOIN servicos s ON a.servico_id = s.id
            INNER JOIN clientes c ON a.cliente_id = c.id
            INNER JOIN barbeiros b ON a.barbeiro_id = b.id
            WHERE 1=1
        ";

    $params = [];

    // Aplicar filtros
    if (isset($filtros['barbeiro_id'])) {
      $sql .= " AND a.barbeiro_id = ?";
      $params[] = $filtros['barbeiro_id'];
    }

    if (isset($filtros['status'])) {
      $sql .= " AND a.status = ?";
      $params[] = $filtros['status'];
    }

    if (isset($filtros['data_inicio'])) {
      $sql .= " AND DATE(a.data_hora) >= ?";
      $params[] = $filtros['data_inicio'];
    }

    if (isset($filtros['data_fim'])) {
      $sql .= " AND DATE(a.data_hora) <= ?";
      $params[] = $filtros['data_fim'];
    }

    if (isset($filtros['cliente_busca']) && !empty($filtros['cliente_busca'])) {
      $sql .= " AND (c.nome LIKE ? OR c.telefone LIKE ?)";
      $busca = '%' . $filtros['cliente_busca'] . '%';
      $params[] = $busca;
      $params[] = $busca;
    }

    $sql .= " ORDER BY a.data_hora DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
  }

  /**
   * Contar agendamentos com filtros
   */
  public function countAgendamentos($filtros = [])
  {
    $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
    $params = [];

    if (isset($filtros['barbeiro_id'])) {
      $sql .= " AND barbeiro_id = ?";
      $params[] = $filtros['barbeiro_id'];
    }

    if (isset($filtros['data_inicio'])) {
      $sql .= " AND DATE(data_hora) >= ?";
      $params[] = $filtros['data_inicio'];
    }

    if (isset($filtros['data_fim'])) {
      $sql .= " AND DATE(data_hora) <= ?";
      $params[] = $filtros['data_fim'];
    }

    if (isset($filtros['status'])) {
      $sql .= " AND status = ?";
      $params[] = $filtros['status'];
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ? (int)$row['total'] : 0;
  }

  /**
   * Buscar todos os agendamentos de um barbeiro em um período
   */
  public function findByBarbeiroPeriodo($barbeiroId, $dataInicio, $dataFim)
  {
    $sql = "
        SELECT a.*,
               s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
               c.nome as cliente_nome, c.telefone as cliente_telefone,
               DATE(a.data_hora) as data_agendamento,
               TIME(a.data_hora) as horario
        FROM {$this->table} a
        INNER JOIN servicos s ON a.servico_id = s.id
        INNER JOIN clientes c ON a.cliente_id = c.id
        WHERE a.barbeiro_id = ?
          AND DATE(a.data_hora) BETWEEN ? AND ?
        ORDER BY a.data_hora ASC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $dataInicio, $dataFim]);
    return $stmt->fetchAll();
  }

  /**
   * Buscar histórico de agendamentos de um cliente com um barbeiro
   */
  public function getByClienteBarbeiro($clienteId, $barbeiroId)
  {
    $sql = "
      SELECT a.*, s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
             b.nome as barbeiro_nome, c.nome as cliente_nome
      FROM {$this->table} a
      INNER JOIN servicos s ON a.servico_id = s.id
      INNER JOIN barbeiros b ON a.barbeiro_id = b.id
      INNER JOIN clientes c ON a.cliente_id = c.id
      WHERE a.cliente_id = ? AND a.barbeiro_id = ?
      ORDER BY a.data_hora DESC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$clienteId, $barbeiroId]);
    return $stmt->fetchAll();
  }
}
