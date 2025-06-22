<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/BarbeiroController.php';

initApp();

$controller = new BarbeiroController();
$controller->finalizarAgendamento();
