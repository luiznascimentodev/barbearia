# 🤝 Guia de Contribuição

Obrigado por considerar contribuir para o Sistema de Agendamento para Barbearias! Este documento fornece diretrizes para contribuições.

## 📋 Índice

- [Código de Conduta](#código-de-conduta)
- [Como Posso Contribuir?](#como-posso-contribuir)
- [Configuração do Ambiente](#configuração-do-ambiente)
- [Processo de Desenvolvimento](#processo-de-desenvolvimento)
- [Padrões de Código](#padrões-de-código)
- [Commits e Pull Requests](#commits-e-pull-requests)
- [Relatando Bugs](#relatando-bugs)
- [Sugerindo Melhorias](#sugerindo-melhorias)

## Código de Conduta

Este projeto segue um código de conduta. Ao participar, você deve manter um ambiente respeitoso e inclusivo.

## Como Posso Contribuir?

### 🐛 Relatando Bugs
- Verifique se o bug já foi reportado nas [Issues](../../issues)
- Use o template de bug report
- Inclua informações detalhadas sobre o ambiente

### 💡 Sugerindo Melhorias
- Verifique se a sugestão já foi feita
- Use o template de feature request
- Explique claramente o problema e a solução proposta

### 💻 Contribuindo com Código
- Fork o repositório
- Crie uma branch para sua feature
- Implemente as mudanças
- Adicione testes se aplicável
- Envie um Pull Request

## Configuração do Ambiente

### Pré-requisitos
```bash
# PHP 8.0+
php --version

# MySQL/MariaDB
mysql --version

# Git
git --version
```

### Setup Local
```bash
# 1. Fork e clone o repositório
git clone https://github.com/SEU-USERNAME/barbearia-agendamento.git
cd barbearia-agendamento

# 2. Configure o banco de dados
mysql -u root -p -e "CREATE DATABASE barbearia_agendamento_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p barbearia_agendamento_dev < database.sql

# 3. Configure a aplicação
cp config/config.example.php config/config.php
# Edite config/config.php com suas credenciais

# 4. Configure o servidor local
php -S localhost:8000 -t public/
```

## Processo de Desenvolvimento

### Workflow de Branches
```
main branch ← develop branch ← feature branches
```

- **main**: Código estável em produção
- **develop**: Integração das novas features
- **feature/nome-da-feature**: Desenvolvimento de novas funcionalidades
- **hotfix/nome-do-fix**: Correções urgentes

### Criando uma Nova Feature
```bash
# 1. Atualize sua branch local
git checkout develop
git pull origin develop

# 2. Crie uma nova branch
git checkout -b feature/nova-funcionalidade

# 3. Desenvolva e teste
# ... faça suas alterações ...

# 4. Commit suas mudanças
git add .
git commit -m "feat: adiciona nova funcionalidade"

# 5. Push para o seu fork
git push origin feature/nova-funcionalidade

# 6. Abra um Pull Request
```

## Padrões de Código

### PHP
- Siga as PSR-4 para autoloading
- Use PSR-12 para estilo de código
- Documente métodos públicos com PHPDoc
- Use type hints sempre que possível

```php
<?php
/**
 * Exemplo de método bem documentado
 *
 * @param string $nome Nome do cliente
 * @param int $idade Idade do cliente
 * @return bool True se sucesso, false caso contrário
 */
public function criarCliente(string $nome, int $idade): bool
{
    // Implementação...
}
```

### JavaScript
- Use ES6+ quando possível
- Mantenha funções pequenas e focadas
- Use nomes descritivos para variáveis

```javascript
// ✅ Bom
const buscarHorariosDisponiveis = async (barbeiroId) => {
    // Implementação...
};

// ❌ Ruim
const bhd = (bid) => {
    // Implementação...
};
```

### CSS
- Use classes semânticas
- Organize por componentes
- Mantenha especificidade baixa

```css
/* ✅ Bom */
.agendamento-card {
    /* estilos */
}

.agendamento-card__titulo {
    /* estilos */
}

/* ❌ Ruim */
.card div h3 {
    /* estilos */
}
```

### SQL
- Use prepared statements sempre
- Nomes de tabelas e colunas em snake_case
- Inclua comentários em consultas complexas

```sql
-- ✅ Bom
SELECT
    a.id,
    a.data_agendamento,
    c.nome as cliente_nome
FROM agendamentos a
INNER JOIN clientes c ON a.cliente_id = c.id
WHERE a.barbeiro_id = ? AND a.status = 'confirmado';
```

## Commits e Pull Requests

### Formato de Commits
Use [Conventional Commits](https://www.conventionalcommits.org/):

```
<tipo>[escopo opcional]: <descrição>

[corpo opcional]

[rodapé opcional]
```

#### Tipos de Commit
- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Mudanças na documentação
- `style`: Formatação, sem mudança de lógica
- `refactor`: Refatoração de código
- `perf`: Melhoria de performance
- `test`: Adicionar ou corrigir testes
- `chore`: Tarefas de manutenção

#### Exemplos
```bash
feat(agendamento): adiciona validação de horário
fix(auth): corrige redirecionamento após login
docs(readme): atualiza instruções de instalação
style(css): corrige indentação nos componentes
```

### Pull Requests

#### Template de PR
```markdown
## 📝 Descrição
Breve descrição das mudanças realizadas.

## 🔧 Tipo de Mudança
- [ ] Bug fix (correção que resolve um problema)
- [ ] Nova feature (funcionalidade que adiciona algo novo)
- [ ] Breaking change (mudança que quebra compatibilidade)
- [ ] Documentação

## ✅ Checklist
- [ ] Código segue os padrões do projeto
- [ ] Testes foram adicionados/atualizados
- [ ] Documentação foi atualizada
- [ ] Mudanças foram testadas localmente

## 🧪 Como Testar
1. Faça checkout da branch
2. Execute `php -S localhost:8000 -t public/`
3. Navegue para [URL específica]
4. Teste a funcionalidade

## 📷 Screenshots (se aplicável)
[Adicione screenshots se a mudança afeta a UI]
```

## Relatando Bugs

### Template de Bug Report
```markdown
## 🐛 Descrição do Bug
Uma descrição clara e concisa do bug.

## 🔄 Passos para Reproduzir
1. Vá para '...'
2. Clique em '....'
3. Role para baixo até '....'
4. Veja o erro

## ✅ Comportamento Esperado
Descrição clara do que deveria acontecer.

## ❌ Comportamento Atual
Descrição do que realmente acontece.

## 🖼️ Screenshots
Se aplicável, adicione screenshots.

## 💻 Ambiente
- OS: [ex: Ubuntu 20.04]
- Navegador: [ex: Chrome 91.0]
- PHP: [ex: 8.0.12]
- MySQL: [ex: 8.0.26]

## 📋 Contexto Adicional
Qualquer outra informação relevante.
```

## Sugerindo Melhorias

### Template de Feature Request
```markdown
## 🚀 Resumo da Feature
Breve descrição da feature proposta.

## 🎯 Problema que Resolve
Que problema esta feature resolve?

## 💡 Solução Proposta
Descrição detalhada da implementação.

## 🔄 Alternativas Consideradas
Outras abordagens que foram consideradas.

## 📋 Contexto Adicional
Screenshots, mockups, links relevantes, etc.
```

## Diretrizes Gerais

### ✅ Faça
- Mantenha o escopo das mudanças pequeno
- Escreva mensagens de commit descritivas
- Adicione testes para novas funcionalidades
- Atualize a documentação
- Siga os padrões de código existentes

### ❌ Não Faça
- Inclua mudanças não relacionadas no mesmo PR
- Faça commits com mensagens vagas
- Submeta código sem testar
- Ignore os padrões de código
- Modifique arquivos de configuração pessoais

## 🏆 Reconhecimento

Todos os contribuidores serão reconhecidos na seção de contribuidores do README.

## 📞 Ajuda

Se você tem dúvidas sobre como contribuir:
- Abra uma [Issue](../../issues) com a tag "question"
- Entre em contato através do email do projeto

---

Obrigado por contribuir! 🎉
