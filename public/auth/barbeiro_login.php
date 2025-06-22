<?php
require_once __DIR__ . '/../../config/config.php';
initApp();
require_once ROOT_PATH . '/controllers/AuthController.php';
$controller = new AuthController();
$controller->barbeiroLogin();
