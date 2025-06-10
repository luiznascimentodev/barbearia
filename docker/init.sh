#!/bin/bash

# Script de inicialização para produção
set -e

echo "🚀 Iniciando aplicação Barbearia..."

# Verificar se estamos em produção
if [ "$ENVIRONMENT" = "production" ]; then
    echo "📦 Ambiente: PRODUÇÃO"

    # Usar configuração de produção
    if [ -f "/var/www/html/config/config.prod.php" ]; then
        cp /var/www/html/config/config.prod.php /var/www/html/config/config.php
        echo "✅ Configuração de produção aplicada"
    fi

    # Aguardar banco de dados ficar disponível
    echo "⏳ Aguardando banco de dados..."
    for i in {1..30}; do
        if php -r "
            try {
                \$pdo = new PDO('mysql:host=${DB_HOST};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
                echo 'Conectado ao banco de dados';
                exit(0);
            } catch (Exception \$e) {
                exit(1);
            }
        " 2>/dev/null; then
            echo "✅ Banco de dados disponível"
            break
        fi
        echo "⏳ Tentativa $i/30..."
        sleep 2
    done

    # Verificar se a estrutura do banco existe
    echo "🔧 Verificando estrutura do banco..."
    php -r "
        try {
            \$pdo = new PDO('mysql:host=${DB_HOST};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
            \$result = \$pdo->query('SHOW TABLES');
            \$tables = \$result->fetchAll(PDO::FETCH_COLUMN);
            if (empty(\$tables)) {
                echo 'Estrutura do banco não encontrada. Execute o script SQL manualmente.';
            } else {
                echo 'Estrutura do banco OK';
            }
        } catch (Exception \$e) {
            echo 'Erro ao verificar banco: ' . \$e->getMessage();
        }
    "
else
    echo "📦 Ambiente: DESENVOLVIMENTO"
fi

echo "✅ Aplicação inicializada com sucesso!"

# Iniciar Apache
exec apache2-foreground
