-- ============================================================
-- SCRIPT DEFINITIVO DE CRIAÇÃO DO BANCO - XPTOTEC BEAUTY
-- ============================================================

CREATE DATABASE IF NOT EXISTS xptotec_salao;
USE xptotec_salao;

-- 1. TABELA DE CLIENTES
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABELA DE FUNCIONÁRIOS (Administradores e Profissionais)
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL UNIQUE,
    senha VARCHAR(255) NULL,
    cargo ENUM('admin', 'profissional') NOT NULL DEFAULT 'profissional',
    especialidade VARCHAR(100) NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABELA DE SERVIÇOS
CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    categoria VARCHAR(50) NOT NULL,
    duracao_minutos INT NOT NULL DEFAULT 30,
    preco DECIMAL(10,2) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABELA DE AGENDAMENTOS
CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servico_id INT NOT NULL,
    funcionario_id INT NOT NULL,
    data_reserva DATE NOT NULL,
    hora_reserva TIME NOT NULL,
    status ENUM('pendente', 'confirmado', 'concluido', 'cancelado') DEFAULT 'confirmado',
    cartao_final VARCHAR(4) NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE RESTRICT,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TABELA DE AVALIAÇÕES
CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL UNIQUE,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT NULL,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. TABELA DE FILA DE ESPERA (Opcional para uso futuro)
CREATE TABLE IF NOT EXISTS fila_espera (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_desejada DATE NOT NULL,
    periodo ENUM('manha', 'tarde', 'noite') NOT NULL,
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- INSERÇÃO DE DADOS INICIAIS
-- ============================================================

-- Criação do Administrador Geral do Sistema
INSERT INTO funcionarios (nome, email, senha, cargo, especialidade) 
VALUES ('Administrador Geral', 'admin@xptotec.com', 'admin123', 'admin', 'Direção')
ON DUPLICATE KEY UPDATE cargo='admin', senha='admin123';

-- Serviços da Categoria "Salão de Beleza" (Coluna Esquerda da Home)
INSERT INTO servicos (nome, descricao, categoria, duracao_minutos, preco) 
VALUES ('Corte de Cabelo Clean', 'Lavagem, corte estilizado e finalização', 'Salão de Beleza', 45, 60.00);

INSERT INTO servicos (nome, descricao, categoria, duracao_minutos, preco) 
VALUES ('Manicure Completa', 'Corte, lixamento, remoção de cutícula e esmaltação tradicional', 'Salão de Beleza', 40, 30.00);

-- Serviço da Categoria "Centro de Estética" (Coluna Direita da Home)
INSERT INTO servicos (nome, descricao, categoria, duracao_minutos, preco) 
VALUES ('Limpeza de Pele Profunda', 'Higienização, esfoliação, extração de cravos e máscara hidratante calmante', 'Centro de Estética', 60, 120.00);

-- Profissionais de Atendimento
INSERT INTO funcionarios (nome, email, senha, cargo, especialidade) 
VALUES ('Claudia', 'claudia@xptotec.com', 'senha123', 'profissional', 'Manicure');

INSERT INTO funcionarios (nome, email, senha, cargo, especialidade) 
VALUES ('Fabiana Silva', 'fabiana@xptotec.com', 'senha123', 'profissional', 'Estética');