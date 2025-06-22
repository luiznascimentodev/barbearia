<?php
// Teste para verificar se a proteção contra redeclaração está funcionando
echo "Incluindo components.php pela primeira vez...\n";
include __DIR__ . '/views/partials/components.php';

echo "Incluindo components.php pela segunda vez...\n";
include __DIR__ . '/views/partials/components.php';

echo "Incluindo components.php pela terceira vez...\n";
include __DIR__ . '/views/partials/components.php';

echo "Teste da função formatDate: " . formatDate('2024-01-15') . "\n";
echo "Teste bem-sucedido! Nenhum erro de redeclaração.\n";
