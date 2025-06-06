USE listaatena;

-- Tabela de utilizadores
CREATE TABLE utilizadores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  tipo ENUM('admin', 'aluno') DEFAULT 'aluno'
);

-- Tabela de eventos
CREATE TABLE eventos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  data DATE NOT NULL,
  hora TIME,
  descricao TEXT
);

-- Tabela de palestras
CREATE TABLE palestras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tema VARCHAR(255) NOT NULL,
  orador VARCHAR(100),
  data DATE,
  descricao TEXT
);
