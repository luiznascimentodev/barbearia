<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ClienteController.php';

initApp();

$controller = new ClienteController();
$controller->agendamentos();
