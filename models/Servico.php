<?php

/**
 * Model para gerenciamento de serviços
 */

require_once 'BaseModel.php';

class Servico extends BaseModel
{
  protected $table = 'servicos';

  /**
   * Buscar serviços por barbeiro
   */
  public function findByBarbeiro($barbeiroId)
  {
    return $this->findAllBy('barbeiro_id', $barbeiroId);
  }

  /**
   * Buscar serviços por barbeiro (compatível com getByBarbeiroId)
   */
  public function getByBarbeiroId($barbeiroId)
  {
    return $this->findByBarbeiro($barbeiroId);
  }

  /**
   * Criar novo serviço
   */
  public function createServico($barbeiroId, $nome, $descricao, $preco)
  {
    $data = [
      'barbeiro_id' => $barbeiroId,
      'nome' => $nome,
      'descricao' => $descricao,
      'preco' => $preco
    ];

    return $this->create($data);
  }

  /**
   * Atualizar serviço
   */
  public function updateServico($id, $nome, $descricao, $preco)
  {
    $data = [
      'nome' => $nome,
      'descricao' => $descricao,
      'preco' => $preco
    ];

    return $this->update($id, $data);
  }

  /**
   * Verificar se serviço pertence ao barbeiro
   */
  public function belongsToBarbeiro($servicoId, $barbeiroId)
  {
    $sql = "SELECT id FROM {$this->table} WHERE id = ? AND barbeiro_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$servicoId, $barbeiroId]);

    return $stmt->fetch() !== false;
  }

  /**
   * Buscar serviço com dados do barbeiro
   */
  public function findWithBarbeiro($servicoId)
  {
    $sql = "
            SELECT s.*, b.nome as barbeiro_nome, b.email as barbeiro_email
            FROM {$this->table} s
            INNER JOIN barbeiros b ON s.barbeiro_id = b.id
            WHERE s.id = ?
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$servicoId]);

    return $stmt->fetch();
  }

  /**
   * Buscar todos os serviços com dados dos barbeiros
   */
  public function findAllWithBarbeiros()
  {
    $sql = "
            SELECT s.*, b.nome as barbeiro_nome, b.email as barbeiro_email
            FROM {$this->table} s
            INNER JOIN barbeiros b ON s.barbeiro_id = b.id
            ORDER BY b.nome, s.nome
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Verificar se serviço tem agendamentos
   */
  public function hasAgendamentos($servicoId)
  {
    $sql = "SELECT COUNT(*) as total FROM agendamentos WHERE servico_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$servicoId]);

    $result = $stmt->fetch();
    return $result['total'] > 0;
  }

  /**
   * Buscar serviços disponíveis (com barbeiros ativos)
   */
  public function findAvailable()
  {
    $sql = "
            SELECT s.*, b.nome as barbeiro_nome
            FROM {$this->table} s
            INNER JOIN barbeiros b ON s.barbeiro_id = b.id
            ORDER BY b.nome, s.nome
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
  }
}
