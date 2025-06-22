

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS barbearia_agendamento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barbearia_agendamento;

-- Tabela de barbeiros
CREATE TABLE barbeiros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Tabela de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_telefone (telefone)
);

-- Tabela de serviços
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barbeiro_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    duracao INT NOT NULL DEFAULT 30,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (barbeiro_id) REFERENCES barbeiros(id) ON DELETE CASCADE,
    INDEX idx_barbeiro (barbeiro_id)
);

-- Tabela de horários disponíveis
CREATE TABLE horarios_disponiveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barbeiro_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    disponivel BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barbeiro_id) REFERENCES barbeiros(id) ON DELETE CASCADE,
    INDEX idx_barbeiro_data (barbeiro_id, data_hora),
    UNIQUE KEY unique_barbeiro_horario (barbeiro_id, data_hora)
);

-- Tabela de agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    barbeiro_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('pendente', 'confirmado', 'cancelado', 'finalizado') DEFAULT 'pendente',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (barbeiro_id) REFERENCES barbeiros(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_barbeiro (barbeiro_id),
    INDEX idx_data_hora (data_hora),
    INDEX idx_status (status)
);

-- Inserção de dados fictícios

-- Barbeiros
INSERT INTO barbeiros (nome, email, senha_hash) VALUES
('João Silva', 'joao@barbearia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- senha: password
('Carlos Santos', 'carlos@barbearia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- senha: password

-- Clientes
INSERT INTO clientes (nome, telefone, email, senha_hash) VALUES
('Maria Oliveira', '(11) 99999-1111', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- senha: password
('Pedro Costa', '(11) 99999-2222', 'pedro@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- senha: password
('Ana Santos', '(11) 99999-3333', 'ana@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- senha: password
('Lucas Ferreira', '(11) 99999-4444', 'lucas@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- senha: password
('Fernanda Lima', '(11) 99999-5555', 'fernanda@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- senha: password

-- Serviços do João (barbeiro_id = 1)
INSERT INTO servicos (barbeiro_id, nome, descricao, preco) VALUES
(1, 'Corte Masculino', 'Corte de cabelo masculino tradicional', 25.00),
(1, 'Barba Completa', 'Aparar e modelar barba completa', 20.00),
(1, 'Corte + Barba', 'Pacote completo de corte e barba', 40.00);

-- Serviços do Carlos (barbeiro_id = 2)
INSERT INTO servicos (barbeiro_id, nome, descricao, preco) VALUES
(2, 'Corte Moderno', 'Corte de cabelo moderno e estilizado', 30.00),
(2, 'Bigode', 'Aparar e modelar bigode', 15.00),
(2, 'Tratamento Capilar', 'Tratamento e hidratação capilar', 50.00);

-- Horários disponíveis para João (próximos 7 dias)
INSERT INTO horarios_disponiveis (barbeiro_id, data_hora) VALUES
(1, '2025-06-05 09:00:00'),
(1, '2025-06-05 10:00:00'),
(1, '2025-06-05 11:00:00'),
(1, '2025-06-05 14:00:00'),
(1, '2025-06-05 15:00:00');

-- Horários disponíveis para Carlos (próximos 7 dias)
INSERT INTO horarios_disponiveis (barbeiro_id, data_hora) VALUES
(2, '2025-06-05 08:00:00'),
(2, '2025-06-05 09:00:00'),
(2, '2025-06-05 10:00:00'),
(2, '2025-06-05 13:00:00'),
(2, '2025-06-05 16:00:00');

-- Alguns agendamentos de exemplo
INSERT INTO agendamentos (cliente_id, barbeiro_id, servico_id, data_hora, status) VALUES
(1, 1, 1, '2025-06-05 09:00:00', 'confirmado'),
(2, 1, 3, '2025-06-05 10:00:00', 'pendente'),
(3, 2, 4, '2025-06-05 08:00:00', 'confirmado');
