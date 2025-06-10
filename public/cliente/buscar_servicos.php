<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Servico.php';

$barbeiro_id = $_GET['barbeiro_id'] ?? null;
if (!$barbeiro_id) {
  echo json_encode([]);
  exit;
}
$servicoModel = new Servico();
$servicos = $servicoModel->findByBarbeiro($barbeiro_id);

$result = [];
foreach ($servicos as $servico) {
  $result[] = [
    'id' => $servico['id'],
    'nome' => $servico['nome']
  ];
}
echo json_encode($result);
