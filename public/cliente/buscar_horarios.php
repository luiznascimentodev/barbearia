<?php

/**
 * Helper para buscar horários disponíveis via AJAX ou form submission
 * Retorna JSON ou redireciona com dados em sessão
 */

require_once __DIR__ . '/../../config/config.php';

// Verificar se cliente está logado
checkClientAuth();

$barbeiro_id = $_GET['barbeiro_id'] ?? null;
$servico_id = $_GET['servico_id'] ?? null;
$data = $_GET['data'] ?? null;
if (!$barbeiro_id || !$servico_id || !$data) {
  echo json_encode([]);
  exit;
}
$horarioModel = new HorarioDisponivel();
$horarios = $horarioModel->findAvailableForBookingByBarbeiroServicoData($barbeiro_id, $servico_id, $data);

// Filtrar apenas horários de segunda a sábado, 8h às 18h
$result = [];
foreach ($horarios as $horario) {
  $dt = new DateTime($horario['data_hora']);
  $dia_semana = (int)$dt->format('N'); // 1=segunda, 7=domingo
  $hora = (int)$dt->format('H');
  if ($dia_semana >= 1 && $dia_semana <= 6 && $hora >= 8 && $hora < 18) {
    $result[] = [
      'id' => $horario['id'],
      'data_hora' => $horario['data_hora'],
      'data_hora_formatada' => formatDateBR($horario['data_hora']),
      'barbeiro_nome' => $horario['barbeiro_nome'] ?? ''
    ];
  }
}
echo json_encode($result);
