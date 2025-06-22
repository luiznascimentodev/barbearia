# 📋 DOCUMENTAÇÃO TÉCNICA DO SISTEMA

## 🏗️ Arquitetura

### Padrão MVC (Model-View-Controller)

- **Models:** Gerenciam dados e lógica de negócio
- **Views:** Interface do usuário (HTML/CSS/Bootstrap)
- **Controllers:** Processam requisições e coordenam Models/Views

### Estrutura de Diretórios

```
final-php/
├── config/           # Configurações da aplicação
├── models/           # Modelos de dados (PDO)
├── controllers/      # Controladores de lógica
├── views/            # Templates e layouts
└── public/           # Arquivos públicos (ponto de entrada)
```

## 🔧 Tecnologias Utilizadas

### Backend

- **PHP 8.4+** - Linguagem principal
- **PDO** - Acesso ao banco de dados
- **MySQL/MariaDB** - Sistema de banco de dados
- **Sessions** - Gerenciamento de autenticação

### Frontend

- **HTML5** - Estrutura semântica
- **Bootstrap 5.3** - Framework CSS responsivo
- **Bootstrap Icons** - Conjunto de ícones
- **CSS3 Custom** - Estilos personalizados com gradientes e animações

### Segurança

- **PDO Prepared Statements** - Proteção contra SQL Injection
- **Password Hashing** - Criptografia de senhas com `password_hash()`
- **Session Security** - Configurações seguras de sessão
- **Input Validation** - Sanitização com `htmlspecialchars()`

## 🗄️ Banco de Dados

### Tabelas Principais

```sql
barbeiros           # Dados dos barbeiros
clientes            # Dados dos clientes
servicos            # Serviços oferecidos pelos barbeiros
agendamentos        # Agendamentos realizados
horarios_disponiveis # Horários configurados pelos barbeiros
```

### Relacionamentos

- `servicos` → `barbeiros` (N:1)
- `agendamentos` → `barbeiros` (N:1)
- `agendamentos` → `clientes` (N:1)
- `agendamentos` → `servicos` (N:1)
- `horarios_disponiveis` → `barbeiros` (N:1)

## 🔐 Sistema de Autenticação

### Tipos de Usuário

1. **Cliente** - Pode agendar serviços e gerenciar perfil
2. **Barbeiro** - Pode gerenciar agenda, serviços e atender clientes

### Fluxo de Autenticação

1. Login via formulário
2. Verificação de credenciais no banco
3. Criação de sessão PHP
4. Redirecionamento para dashboard correspondente

### Proteção de Rotas

- Middleware de verificação em cada página protegida
- Redirecionamento automático para login se não autenticado
- Verificação de tipo de usuário para áreas específicas

## 🎨 Design System

### Cores Principais

```css
--primary-color: #212529    /* Preto/Cinza escuro */
--accent-color: #ffc107     /* Amarelo dourado */
--success-color: #198754    /* Verde */
--danger-color: #dc3545     /* Vermelho */
```

### Componentes

- **Cards** - Containers com sombras e bordas arredondadas
- **Buttons** - Gradientes e efeitos hover
- **Forms** - Inputs com bordas personalizadas
- **Tables** - Cabeçalhos estilizados e hover effects

### Responsividade

- **Mobile First** - Design otimizado para mobile
- **Breakpoints Bootstrap** - sm, md, lg, xl
- **Grid System** - Layout flexível com 12 colunas

## 📱 Funcionalidades por Módulo

### Cliente

```php
// Dashboard
- Próximos agendamentos
- Estatísticas pessoais
- Links rápidos

// Agendamento
- Busca de barbeiros
- Seleção de serviços
- Escolha de horários
- Confirmação

// Perfil
- Edição de dados
- Alteração de senha
- Histórico de agendamentos
```

### Barbeiro

```php
// Dashboard
- Estatísticas do negócio
- Agendamentos do dia
- Links de gestão

// Serviços
- CRUD completo
- Ativação/desativação
- Preços e descrições

// Horários
- Configuração semanal
- Horários específicos
- Remoção em lote

// Agendamentos
- Visualização com filtros
- Mudança de status
- Gestão de clientes
```

## 🔄 Fluxo de Agendamento

### 1. Cliente

```
Buscar Barbeiro → Escolher Serviço → Selecionar Horário → Confirmar
```

### 2. Sistema

```
Verificar Disponibilidade → Criar Agendamento → Notificar Barbeiro
```

### 3. Barbeiro

```
Receber Notificação → Confirmar/Rejeitar → Atender Cliente → Finalizar
```

## 🛡️ Medidas de Segurança Implementadas

### Prevenção de Ataques

- **SQL Injection** - PDO Prepared Statements
- **XSS** - htmlspecialchars() em todas as saídas
- **CSRF** - Verificação de origem das requisições
- **Session Hijacking** - Configurações seguras de sessão

### Validação de Dados

- **Server-side** - Validação no PHP antes de inserir no banco
- **Client-side** - HTML5 validation para UX
- **Sanitização** - Limpeza de dados de entrada

### Controle de Acesso

- **Autenticação** - Verificação de login em todas as páginas
- **Autorização** - Verificação de permissões por tipo de usuário
- **Session Management** - Timeout e regeneração de ID

## 🚀 Performance

### Otimizações

- **Consultas Eficientes** - Indexes nas tabelas principais
- **Cache de Sessão** - Reutilização de dados do usuário
- **CSS/JS Minificado** - Arquivos otimizados
- **Imagens Otimizadas** - Uso de ícones SVG

### Escalabilidade

- **Separação de Concerns** - Arquitetura modular
- **Database Design** - Normalização adequada
- **Code Reusability** - Classes base reutilizáveis

## 📊 Monitoramento

### Logs

- **Errors** - error_log() para erros PHP
- **Authentication** - Log de tentativas de login
- **Database** - Queries com problemas

### Métricas

- **Agendamentos** - Por barbeiro/período
- **Usuários Ativos** - Clientes e barbeiros
- **Performance** - Tempo de resposta

## 🔧 Manutenção

### Backup

```sql
mysqldump -u root -p barbearia_agendamento > backup.sql
```

### Updates

```bash
git pull origin main
php composer.phar update  # Se usar Composer no futuro
```

**Sistema desenvolvido seguindo as melhores práticas de desenvolvimento web com PHP.**
