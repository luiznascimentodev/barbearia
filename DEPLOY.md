# 🚀 Deploy - Guia de Configuração Docker

Este guia explica como configurar e fazer deploy da aplicação usando Docker, tanto para desenvolvimento local quanto para produção no Render.com.

## 📋 Índice

- [Desenvolvimento Local](#desenvolvimento-local)
- [Deploy no Render.com](#deploy-no-rendercom)
- [Configurações de Ambiente](#configurações-de-ambiente)
- [Monitoramento](#monitoramento)
- [Troubleshooting](#troubleshooting)

## 🔧 Desenvolvimento Local

### Pré-requisitos
- Docker 20.0+
- Docker Compose 2.0+

### Executar com Docker Compose

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/barbearia-agendamento.git
cd barbearia-agendamento

# 2. Inicie os serviços
docker-compose up -d

# 3. Verifique se os containers estão rodando
docker-compose ps

# 4. Acesse a aplicação
# Aplicação: http://localhost:8080
# phpMyAdmin: http://localhost:8081
```

### Comandos Úteis

```bash
# Ver logs da aplicação
docker-compose logs app

# Ver logs do banco de dados
docker-compose logs db

# Entrar no container da aplicação
docker-compose exec app bash

# Parar todos os serviços
docker-compose down

# Rebuild dos containers
docker-compose up --build
```

## 🌐 Deploy no Render.com

### 1. Preparação do Repositório

```bash
# 1. Certifique-se de que todos os arquivos estão commitados
git add .
git commit -m "feat: adiciona configuração Docker para produção"
git push origin main

# 2. Verifique se os arquivos necessários estão presentes:
# - Dockerfile.prod
# - render.yaml
# - config/config.prod.php
```

### 2. Configuração no Render.com

1. **Faça login no Render.com**
   - Acesse [render.com](https://render.com)
   - Conecte sua conta GitHub

2. **Crie um novo serviço**
   - Clique em "New +"
   - Selecione "Web Service"
   - Conecte seu repositório GitHub

3. **Configurar o serviço**
   ```yaml
   Name: barbearia-agendamento
   Environment: Docker
   Dockerfile Path: ./Dockerfile.prod
   Branch: main
   ```

4. **Criar banco de dados**
   - Clique em "New +"
   - Selecione "PostgreSQL" ou "MySQL"
   - Configure:
     ```
     Name: barbearia-db
     Database Name: barbearia_agendamento
     User: barbearia_user
     ```

5. **Configurar variáveis de ambiente**
   No painel do seu web service, adicione:
   ```
   ENVIRONMENT=production
   DB_HOST=[URL do banco do Render]
   DB_NAME=barbearia_agendamento
   DB_USER=barbearia_user
   DB_PASS=[senha do banco]
   ```

### 3. Executar Migração do Banco

Após o deploy, você precisa executar o SQL do banco:

```bash
# 1. Conecte ao banco de dados do Render
# (Use as credenciais fornecidas pelo Render)

# 2. Execute o script SQL
mysql -h [host] -u [user] -p [database] < database.sql
```

## ⚙️ Configurações de Ambiente

### Variáveis de Ambiente

| Variável | Desenvolvimento | Produção | Descrição |
|----------|----------------|----------|-----------|
| `ENVIRONMENT` | `development` | `production` | Ambiente da aplicação |
| `DB_HOST` | `db` | `[render-host]` | Host do banco de dados |
| `DB_NAME` | `barbearia_agendamento` | `barbearia_agendamento` | Nome do banco |
| `DB_USER` | `barbearia_user` | `barbearia_user` | Usuário do banco |
| `DB_PASS` | `barbearia_pass` | `[render-password]` | Senha do banco |

### Configurações PHP

#### Desenvolvimento (`docker/php.ini`)
- `display_errors = On` - Para debug
- `opcache.validate_timestamps = 1` - Para mudanças em tempo real

#### Produção (`docker/php.prod.ini`)
- `display_errors = Off` - Segurança
- `opcache.validate_timestamps = 0` - Performance máxima
- `memory_limit = 512M` - Mais recursos

## 📊 Monitoramento

### Health Checks

A aplicação inclui health checks automáticos:

```dockerfile
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
```

### Logs

#### Desenvolvimento
```bash
# Logs em tempo real
docker-compose logs -f app

# Logs do banco
docker-compose logs -f db
```

#### Produção (Render.com)
- Acesse o painel do Render
- Vá em "Logs" do seu serviço
- Monitore erros e performance

### Métricas Importantes

- **Response Time** - < 500ms
- **Memory Usage** - < 80% do limite
- **CPU Usage** - < 70%
- **Database Connections** - Monitorar pool

## 🔧 Troubleshooting

### Problemas Comuns

#### 1. Erro de Conexão com Banco

```bash
# Verificar se o banco está rodando
docker-compose ps

# Verificar logs do banco
docker-compose logs db

# Testar conexão manual
docker-compose exec app php -r "
try {
    \$pdo = new PDO('mysql:host=db;dbname=barbearia_agendamento', 'barbearia_user', 'barbearia_pass');
    echo 'Conexão OK\n';
} catch (Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . '\n';
}
"
```

#### 2. Problemas de Permissão

```bash
# Corrigir permissões
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html
```

#### 3. Erro 500 - Internal Server Error

```bash
# Verificar logs do Apache
docker-compose exec app tail -f /var/log/apache2/error.log

# Verificar logs PHP
docker-compose exec app tail -f /var/log/php_errors.log
```

#### 4. Performance Lenta

```bash
# Verificar uso de recursos
docker stats

# Otimizar OPcache
# Edite docker/php.prod.ini e rebuilde o container
```

### Comandos de Debug

```bash
# Entrar no container
docker-compose exec app bash

# Verificar configuração PHP
docker-compose exec app php -i

# Testar conectividade
docker-compose exec app ping db

# Verificar estrutura do banco
docker-compose exec db mysql -u root -p -e "SHOW DATABASES;"
```

## 🚀 Scripts de Deploy

### Script de Deploy Local

```bash
#!/bin/bash
# deploy-local.sh

echo "🚀 Iniciando deploy local..."

# Parar serviços existentes
docker-compose down

# Rebuild com cache limpo
docker-compose build --no-cache

# Iniciar serviços
docker-compose up -d

# Aguardar serviços ficarem prontos
sleep 10

# Verificar status
docker-compose ps

echo "✅ Deploy local concluído!"
echo "🌐 Aplicação: http://localhost:8080"
echo "🗄️ phpMyAdmin: http://localhost:8081"
```

### Script de Backup

```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

echo "📦 Criando backup do banco de dados..."

docker-compose exec -T db mysqldump \
  -u root -prootpassword \
  barbearia_agendamento > "$BACKUP_DIR/backup_$DATE.sql"

echo "✅ Backup salvo em: $BACKUP_DIR/backup_$DATE.sql"
```

## 📚 Recursos Adicionais

### Links Úteis
- [Docker Documentation](https://docs.docker.com/)
- [Render.com Docs](https://render.com/docs)
- [PHP Docker Official](https://hub.docker.com/_/php)
- [MySQL Docker Official](https://hub.docker.com/_/mysql)

### Próximos Passos
- [ ] Configurar CI/CD com GitHub Actions
- [ ] Implementar monitoramento com Prometheus
- [ ] Configurar backup automático
- [ ] Otimizar performance com Redis
- [ ] Implementar load balancing

---

**Desenvolvido com ❤️ pela equipe Barbearia**
