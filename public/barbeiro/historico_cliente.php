<?php
require_once __DIR__ . '/../../config/config.php';
initApp();

header('Content-Type: application/json');

if (!isset($_SESSION['barbeiro_id'])) {
  http_response_code(403);
  echo json_encode(['error' => 'Não autorizado']);
  exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];
$cliente_id = $_GET['cliente_id'] ?? null;

if (!$cliente_id || !is_numeric($cliente_id)) {
  http_response_code(400);
  echo json_encode(['error' => 'Cliente inválido']);
  exit;
}

require_once __DIR__ . '/../../models/Agendamento.php';
$agendamento = new Agendamento();
$historico = $agendamento->getByClienteBarbeiro($cliente_id, $barbeiro_id);

if (!$historico) {
  echo json_encode([]);
  exit;
}

foreach ($historico as &$item) {
  $item['data_hora'] = date('d/m/Y H:i', strtotime($item['data_hora']));
}
echo json_encode($historico);
