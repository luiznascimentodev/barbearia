# Sistema de Agendamento para Barbearias

Um sistema completo de agendamento online para barbearias, desenvolvido em PHP puro com MVC, Bootstrap e MariaDB/MySQL.

## 🚀 Características

- **Arquitetura MVC** - Organização clara do código com separação de responsabilidades
- **Dois tipos de usuário**: Clientes e Barbeiros
- **Interface responsiva** com Bootstrap 5
- **Segurança** com proteção contra SQL Injection usando PDO
- **Autenticação** baseada em sessões PHP
- **Sistema completo** de CRUD para agendamentos, serviços e horários

## 📋 Pré-requisitos

- PHP 7.4+
- MariaDB 10.3+ ou MySQL 5.7+
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone <repo-url>
cd final-php
```

### 2. Configure o banco de dados

```sql
-- Crie um banco de dados
CREATE DATABASE barbearia_agendamento;

-- Importe o schema
mysql -u root -p barbearia_agendamento < database.sql
```

### 3. Configure a conexão

Edite o arquivo `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'barbearia_agendamento');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 4. Configure o servidor web

Aponte o DocumentRoot para a pasta `public/`:

**Apache (.htaccess já incluído):**

```apache
DocumentRoot /caminho/para/projeto/public
```

**Nginx:**

```nginx
root /caminho/para/projeto/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### 5. Permissões (Linux/Mac)

```bash
sudo chown -R www-data:www-data ./
sudo chmod -R 755 ./
```

## 📚 Estrutura do Projeto

```
final-php/
├── config/
│   └── config.php              # Configurações e funções globais
├── models/
│   ├── BaseModel.php           # Classe base para modelos
│   ├── Cliente.php             # Model de clientes
│   ├── Barbeiro.php            # Model de barbeiros
│   ├── Agendamento.php         # Model de agendamentos
│   ├── Servico.php             # Model de serviços
│   └── Horario.php             # Model de horários
├── controllers/
│   ├── BaseController.php      # Controller base
│   ├── ClienteController.php   # Controller de clientes
│   └── BarbeiroController.php  # Controller de barbeiros
├── views/
│   ├── layouts/
│   │   ├── header.php          # Header comum
│   │   └── footer.php          # Footer comum
│   └── partials/
│       ├── messages.php        # Exibição de mensagens
│       └── components.php      # Componentes reutilizáveis
└── public/
    ├── index.php               # Landing page e router principal
    ├── assets/css/style.css    # Estilos customizados
    ├── auth/                   # Páginas de login/registro
    ├── cliente/                # Área do cliente
    └── barbeiro/               # Área do barbeiro
```

## 👥 Funcionalidades

### Para Clientes

- ✅ Registro e login
- ✅ Busca de barbeiros e serviços
- ✅ Agendamento de horários
- ✅ Visualização de agendamentos
- ✅ Cancelamento de agendamentos
- ✅ Perfil e alteração de dados

### Para Barbeiros

- ✅ Registro e login profissional
- ✅ Dashboard com estatísticas
- ✅ Gerenciamento de serviços (CRUD)
- ✅ Configuração de horários disponíveis
- ✅ Gestão de agendamentos (confirmar/cancelar/finalizar)
- ✅ Lista de clientes
- ✅ Perfil profissional

## 🔐 Segurança

- **PDO com Prepared Statements** - Proteção contra SQL Injection
- **Password Hashing** - Senhas criptografadas com `password_hash()`
- **Validação de Sessões** - Verificação de autenticação em todas as páginas protegidas
- **Sanitização de Dados** - Limpeza de inputs com `htmlspecialchars()`
- **CSRF Protection** - Verificação de origem das requisições

## 🎨 Interface

- **Bootstrap 5** - Design responsivo e moderno
- **Bootstrap Icons** - Ícones consistentes
- **CSS Customizado** - Gradientes, animações e efeitos visuais
- **Mobile-First** - Otimizado para dispositivos móveis

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:

- 📱 Smartphones (320px+)
- 📱 Tablets (768px+)
- 💻 Desktops (1024px+)
- 🖥️ Monitores grandes (1440px+)

## 🚦 Status do Projeto

- ✅ **Completo** - Backend MVC implementado
- ✅ **Completo** - Autenticação e sessões
- ✅ **Completo** - CRUD de todos os módulos
- ✅ **Completo** - Interface responsiva
- ✅ **Completo** - Validações e segurança
- ✅ **Completo** - Landing page e navegação

## 🧪 Testando o Sistema

### 1. Acesse a landing page

Abra `http://seu-dominio/` no navegador

### 2. Registre-se como barbeiro

- Clique em "Sou Barbeiro"
- Preencha os dados e registre-se
- Faça login e configure serviços e horários

### 3. Registre-se como cliente

- Clique em "Sou Cliente"
- Preencha os dados e registre-se
- Faça login e teste o agendamento

### 4. Teste o fluxo completo

- Cliente agenda serviço
- Barbeiro confirma/gerencia agendamento
- Cliente visualiza status

## 🛠️ Dados de Teste

O arquivo `database.sql` inclui dados de exemplo:

**Barbeiro de teste:**

- Email: `joao@barbeiro.com`
- Senha: `123456`

**Cliente de teste:**

- Email: `maria@cliente.com`
- Senha: `123456`

## 📋 To-Do / Melhorias Futuras

- [ ] Sistema de avaliações
- [ ] Notificações por email/SMS
- [ ] Relatórios avançados
- [ ] API REST
- [ ] App mobile
- [ ] Sistema de pagamento online
- [ ] Chat entre cliente e barbeiro


-