<?php

/**
 * Model para gerenciamento de horários de barbeiros
 */

require_once 'BaseModel.php';

class Horario extends BaseModel
{
  protected $table = 'horarios';

  /**
   * Buscar horários por barbeiro
   */
  public function findByBarbeiro($barbeiroId)
  {
    $sql = "
            SELECT * FROM {$this->table}
            WHERE barbeiro_id = ?
            ORDER BY data, hora_inicio
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId]);

    return $stmt->fetchAll();
  }

  /**
   * Buscar horários por barbeiro e data
   */
  public function findByBarbeiroAndDate($barbeiroId, $data)
  {
    $sql = "
            SELECT * FROM {$this->table}
            WHERE barbeiro_id = ? AND data = ?
            ORDER BY hora_inicio
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data]);

    return $stmt->fetchAll();
  }

  /**
   * Verificar se horário está disponível
   */
  public function isAvailable($barbeiroId, $data, $horaInicio)
  {
    // Verificar se existe o horário configurado
    $sql = "
            SELECT id FROM {$this->table}
            WHERE barbeiro_id = ? AND data = ? AND hora_inicio <= ? AND hora_fim > ?
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data, $horaInicio, $horaInicio]);

    if (!$stmt->fetch()) {
      return false; // Horário não configurado
    }

    // Verificar se não há agendamento para este horário
    $sql = "
            SELECT id FROM agendamentos
            WHERE barbeiro_id = ? AND DATE(data_hora) = ? AND TIME(data_hora) = ?
            AND status NOT IN ('cancelado')
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data, $horaInicio]);

    return $stmt->fetch() === false; // Retorna true se não há agendamento
  }

  /**
   * Buscar horários disponíveis para agendamento
   */
  public function getAvailableSlots($barbeiroId, $data)
  {
    $sql = "
            SELECT h.*,
                   CASE WHEN a.id IS NOT NULL THEN 0 ELSE 1 END as disponivel
            FROM {$this->table} h
            LEFT JOIN agendamentos a ON h.barbeiro_id = a.barbeiro_id
                AND DATE(a.data_hora) = h.data
                AND TIME(a.data_hora) BETWEEN h.hora_inicio AND h.hora_fim
                AND a.status NOT IN ('cancelado')
            WHERE h.barbeiro_id = ? AND h.data = ?
            ORDER BY h.hora_inicio
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $data]);

    return $stmt->fetchAll();
  }

  /**
   * Criar horários em lote
   */
  public function createBatch($horarios)
  {
    $sql = "INSERT INTO {$this->table} (barbeiro_id, data, hora_inicio, hora_fim) VALUES (?, ?, ?, ?)";
    $stmt = $this->db->prepare($sql);

    $this->db->beginTransaction();

    try {
      foreach ($horarios as $horario) {
        $stmt->execute([
          $horario['barbeiro_id'],
          $horario['data'],
          $horario['hora_inicio'],
          $horario['hora_fim']
        ]);
      }

      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  /**
   * Remover horários por período
   */
  public function deleteByPeriod($barbeiroId, $dataInicio, $dataFim)
  {
    $sql = "DELETE FROM {$this->table} WHERE barbeiro_id = ? AND data BETWEEN ? AND ?";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([$barbeiroId, $dataInicio, $dataFim]);
  }

  /**
   * Buscar horários por período
   */
  public function getHorariosByPeriodo($barbeiroId, $dataInicio, $dataFim)
  {
    $sql = "
            SELECT h.*,
                   COUNT(a.id) as total_agendamentos,
                   COUNT(CASE WHEN a.status = 'confirmado' THEN 1 END) as agendamentos_confirmados
            FROM {$this->table} h
            LEFT JOIN agendamentos a ON h.barbeiro_id = a.barbeiro_id
                AND DATE(a.data_hora) = h.data
                AND TIME(a.data_hora) BETWEEN h.hora_inicio AND h.hora_fim
            WHERE h.barbeiro_id = ? AND h.data BETWEEN ? AND ?
            GROUP BY h.id
            ORDER BY h.data, h.hora_inicio
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$barbeiroId, $dataInicio, $dataFim]);

    return $stmt->fetchAll();
  }

  /**
   * Gerar horários automáticos para uma semana
   */
  public function generateWeeklySchedule($barbeiroId, $dataInicio, $horaInicio, $horaFim, $intervalos, $diasSemana = [1, 2, 3, 4, 5, 6])
  {
    $horarios = [];
    $data = new DateTime($dataInicio);

    // Gerar horários para 7 dias
    for ($i = 0; $i < 7; $i++) {
      $diaSemana = (int)$data->format('N'); // 1 = segunda, 7 = domingo

      if (in_array($diaSemana, $diasSemana)) {
        $horaAtual = new DateTime($data->format('Y-m-d') . ' ' . $horaInicio);
        $horaFinal = new DateTime($data->format('Y-m-d') . ' ' . $horaFim);

        while ($horaAtual < $horaFinal) {
          $horaProxima = clone $horaAtual;
          $horaProxima->add(new DateInterval('PT' . $intervalos . 'M'));

          if ($horaProxima <= $horaFinal) {
            $horarios[] = [
              'barbeiro_id' => $barbeiroId,
              'data' => $data->format('Y-m-d'),
              'hora_inicio' => $horaAtual->format('H:i:s'),
              'hora_fim' => $horaProxima->format('H:i:s')
            ];
          }

          $horaAtual = $horaProxima;
        }
      }

      $data->add(new DateInterval('P1D'));
    }

    return $horarios;
  }
}
