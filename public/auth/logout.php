<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

initApp();

$controller = new AuthController();
$controller->logout();
