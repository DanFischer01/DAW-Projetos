CREATE DATABASE IF NOT EXISTS salao_beleza;
USE salao_beleza;

-- Tabela de Serviços
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especialidade VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    duracao_minutos INT NOT NULL
);

-- Tabela de Profissionais
CREATE TABLE profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especialidade VARCHAR(50) NOT NULL
);

-- Tabela de Agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servico INT,
    id_profissional INT,
    nome_cliente VARCHAR(100) NOT NULL,
    data_hora DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'Confirmado', -- Confirmado, Cancelado, Falta
    FOREIGN KEY (id_servico) REFERENCES servicos(id),
    FOREIGN KEY (id_profissional) REFERENCES profissionais(id)
);

-- Inserindo dados de teste
INSERT INTO servicos (nome, especialidade, preco, duracao_minutos) VALUES
('Corte de Cabelo', 'Cabelo', 80.00, 60),
('Manicure', 'Unhas', 40.00, 30),
('Limpeza de Pele', 'Estética', 120.00, 45);

INSERT INTO profissionais (nome, especialidade) VALUES
('Ana Silva', 'Cabelo'),
('Beatriz Souza', 'Unhas'),
('Carla Costa', 'Estética'),
('Daniela Lima', 'Cabelo');