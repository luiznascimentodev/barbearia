<?php

/**
 * Model para gerenciamento de horários disponíveis
 */

require_once __DIR__ . '/BaseModel.php';

class HorarioDisponivel extends BaseModel
{
  protected $table = 'horarios_disponiveis';

  /**
   * Buscar horários por barbeiro
   */
  public function findByBarbeiro($barbeiroId)
  {
    $sql = "
            SELECT * FROM {$this->table}
            WHERE barbeiro_id = ? AND data_hora >= NOW()
            ORDER BY data_hora ASC
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);

    return $stmt->fetchAll();
  }

  /**
   * Buscar horários disponíveis (não agendados) por barbeiro
   */
  public function findAvailableByBarbeiro($barbeiroId)
  {
    $sql = "
            SELECT hd.*
            FROM {$this->table} hd
            LEFT JOIN agendamentos a ON hd.barbeiro_id = a.barbeiro_id
                AND hd.data_hora = a.data_hora
                AND a.status != 'cancelado'
            WHERE hd.barbeiro_id = ?
                AND hd.data_hora >= NOW()
                AND a.id IS NULL
                AND hd.disponivel = 1
            ORDER BY hd.data_hora ASC
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);

    return $stmt->fetchAll();
  }

  /**
   * Criar novo horário disponível
   */
  public function createHorario($barbeiroId, $dataHora)
  {
    // Verificar se horário já existe
    if ($this->horarioExists($barbeiroId, $dataHora)) {
      return false;
    }

    $data = [
      'barbeiro_id' => $barbeiroId,
      'data_hora' => $dataHora,
      'disponivel' => 1
    ];

    return $this->create($data);
  }

  /**
   * Verificar se horário já existe
   */
  public function horarioExists($barbeiroId, $dataHora)
  {
    $sql = "SELECT id FROM {$this->table} WHERE barbeiro_id = ? AND data_hora = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $dataHora]);

    return $stmt->fetch() !== false;
  }

  /**
   * Marcar horário como indisponível
   */
  public function markUnavailable($id)
  {
    return $this->update($id, ['disponivel' => 0]);
  }

  /**
   * Marcar horário como disponível
   */
  public function markAvailable($id)
  {
    return $this->update($id, ['disponivel' => 1]);
  }

  /**
   * Verificar se horário pertence ao barbeiro
   */
  public function belongsToBarbeiro($horarioId, $barbeiroId)
  {
    $sql = "SELECT id FROM {$this->table} WHERE id = ? AND barbeiro_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$horarioId, $barbeiroId]);

    return $stmt->fetch() !== false;
  }

  /**
   * Remover horários antigos
   */
  public function removeOldHorarios()
  {
    $sql = "DELETE FROM {$this->table} WHERE data_hora < NOW()";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute();
  }

  /**
   * Buscar horários disponíveis para agendamento (com informações do barbeiro)
   */
  public function findAvailableForBooking($servicoId = null)
  {
    $sql = "
            SELECT hd.*, b.nome as barbeiro_nome
            FROM {$this->table} hd
            INNER JOIN barbeiros b ON hd.barbeiro_id = b.id
            LEFT JOIN agendamentos a ON hd.barbeiro_id = a.barbeiro_id
                AND hd.data_hora = a.data_hora
                AND a.status != 'cancelado'
            WHERE hd.data_hora >= NOW()
                AND a.id IS NULL
                AND hd.disponivel = 1
        ";

    // Se um serviço específico for informado, filtrar por barbeiro que oferece o serviço
    if ($servicoId) {
      $sql .= " AND EXISTS (
                SELECT 1 FROM servicos s
                WHERE s.barbeiro_id = hd.barbeiro_id AND s.id = ?
            )";
    }

    $sql .= " ORDER BY hd.data_hora ASC";

    $stmt = $this->db->prepare($sql);

    if ($servicoId) {
      $stmt->execute([$servicoId]);
    } else {
      $stmt->execute();
    }

    return $stmt->fetchAll();
  }

  /**
   * Buscar horários com status de agendamento
   */
  public function findWithAgendamentoStatus($barbeiroId)
  {
    $sql = "
            SELECT hd.*,
                   a.id as agendamento_id,
                   a.status as agendamento_status,
                   c.nome as cliente_nome,
                   s.nome as servico_nome
            FROM {$this->table} hd
            LEFT JOIN agendamentos a ON hd.barbeiro_id = a.barbeiro_id
                AND hd.data_hora = a.data_hora
                AND a.status != 'cancelado'
            LEFT JOIN clientes c ON a.cliente_id = c.id
            LEFT JOIN servicos s ON a.servico_id = s.id
            WHERE hd.barbeiro_id = ? AND hd.data_hora >= NOW()
            ORDER BY hd.data_hora ASC
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);

    return $stmt->fetchAll();
  }

  /**
   * Buscar horários por período para um barbeiro
   * @param int $barbeiroId
   * @param string $inicio (Y-m-d H:i:s)
   * @param string $fim (Y-m-d H:i:s)
   * @return array
   */
  public function getHorariosByPeriodo($barbeiroId, $inicio, $fim)
  {
    $sql = "SELECT * FROM {$this->table} WHERE barbeiro_id = ? AND data_hora BETWEEN ? AND ? ORDER BY data_hora ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $inicio, $fim]);
    return $stmt->fetchAll();
  }

  /**
   * Reativa um horário tornando-o disponível novamente
   * @param int $barbeiroId
   * @param string $data (Y-m-d)
   * @param string $hora (H:i:s)
   * @return bool
   */
  public function reativarHorario($barbeiroId, $data, $hora)
  {
    $dataHora = $data . ' ' . $hora;
    $sql = "UPDATE {$this->table} SET disponivel = 1 WHERE barbeiro_id = ? AND data_hora = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$barbeiroId, $dataHora]);
  }

  /**
   * Cria horários disponíveis para uma semana inteira
   * @param int $barbeiroId
   * @param string $dataInicio (Y-m-d)
   * @param string $horaInicio (H:i)
   * @param string $horaFim (H:i)
   * @param int $intervalo (minutos)
   * @param array $diasSemana (0=Domingo, 1=Segunda...)
   * @return int Quantidade de horários criados
   */
  public function criarHorariosSemana($barbeiroId, $dataInicio, $horaInicio, $horaFim, $intervalo, $diasSemana)
  {
    $data = new DateTime($dataInicio);
    $fimSemana = clone $data;
    $fimSemana->modify('+6 days');
    $criados = 0;
    while ($data <= $fimSemana) {
      if (in_array((int)$data->format('w'), $diasSemana)) {
        $criados += $this->criarHorariosDia($barbeiroId, $data->format('Y-m-d'), $horaInicio, $horaFim, $intervalo);
      }
      $data->modify('+1 day');
    }
    return $criados;
  }

  /**
   * Cria horários disponíveis para um dia
   * @param int $barbeiroId
   * @param string $data (Y-m-d)
   * @param string $horaInicio (H:i)
   * @param string $horaFim (H:i)
   * @param int $intervalo (minutos)
   * @return int Quantidade de horários criados
   */
  public function criarHorariosDia($barbeiroId, $data, $horaInicio, $horaFim, $intervalo)
  {
    $inicio = new DateTime($data . ' ' . $horaInicio);
    $fim = new DateTime($data . ' ' . $horaFim);
    $criados = 0;
    while ($inicio < $fim) {
      $dataHora = $inicio->format('Y-m-d H:i:s');
      // Só cria se não existir
      if (!$this->horarioExists($barbeiroId, $dataHora)) {
        $this->create(['barbeiro_id' => $barbeiroId, 'data_hora' => $dataHora, 'disponivel' => 1]);
        $criados++;
      }
      $inicio->modify('+' . $intervalo . ' minutes');
    }
    return $criados;
  }

  /**
   * Remove todos os horários de um barbeiro em uma data específica
   * @param int $barbeiroId
   * @param string $data (Y-m-d)
   * @return int Quantidade de horários removidos
   */
  public function removerHorariosByData($barbeiroId, $data)
  {
    $sql = "DELETE FROM {$this->table} WHERE barbeiro_id = ? AND DATE(data_hora) = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data]);
    return $stmt->rowCount();
  }

  /**
   * Buscar horários disponíveis para um barbeiro em uma data específica
   * @param int $barbeiroId
   * @param string $data (Y-m-d)
   * @return array
   */
  public function getHorariosDisponiveis($barbeiroId, $data)
  {
    $sql = "SELECT * FROM {$this->table} WHERE barbeiro_id = ? AND DATE(data_hora) = ? AND disponivel = 1 ORDER BY data_hora ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data]);
    return $stmt->fetchAll();
  }

  /**
   * Buscar horários disponíveis para um barbeiro, serviço e data específica
   */
  public function findAvailableForBookingByBarbeiroServicoData($barbeiroId, $servicoId, $data)
  {
    $sql = "
      SELECT hd.*, b.nome as barbeiro_nome
      FROM {$this->table} hd
      INNER JOIN barbeiros b ON hd.barbeiro_id = b.id
      LEFT JOIN agendamentos a ON hd.barbeiro_id = a.barbeiro_id
        AND hd.data_hora = a.data_hora
        AND a.status != 'cancelado'
      WHERE hd.barbeiro_id = ?
        AND a.id IS NULL
        AND hd.disponivel = 1
        AND hd.data_hora >= NOW()
        AND DATE(hd.data_hora) = ?
        AND EXISTS (
          SELECT 1 FROM servicos s WHERE s.barbeiro_id = hd.barbeiro_id AND s.id = ?
        )
      ORDER BY hd.data_hora ASC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data, $servicoId]);
    return $stmt->fetchAll();
  }
}
