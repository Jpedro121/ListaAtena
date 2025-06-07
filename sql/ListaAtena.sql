-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07-Jun-2025 às 15:56
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `listaatena`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `data` date NOT NULL,
  `hora` time DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `local` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `tipo` enum('evento','palestra') DEFAULT 'evento',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `informacoes`
--

CREATE TABLE `informacoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `data_publicacao` date NOT NULL DEFAULT curdate(),
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `inscricoes_eventos`
--

CREATE TABLE `inscricoes_eventos` (
  `id` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_tentativas`
--

CREATE TABLE `login_tentativas` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `sucesso` tinyint(1) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `palestras`
--

CREATE TABLE `palestras` (
  `id` int(11) NOT NULL,
  `tema` varchar(255) NOT NULL,
  `orador` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `local` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','aluno') DEFAULT 'aluno',
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `tentativas_login` int(11) DEFAULT 0,
  `bloqueado_ate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `username`, `senha`, `tipo`, `data_registo`, `ultimo_login`, `ativo`, `tentativas_login`, `bloqueado_ate`) VALUES
(4, 'Administrador', 'admin@example.com', 'admin', '$2y$10$1fYV.z0wAX4Ue.zq8cHhvOjFpi0Mp1YhfB4oSH5npBTJ.VZoZsM4i', 'admin', '2025-06-07 13:42:16', NULL, 1, 3, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `informacoes`
--
ALTER TABLE `informacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilizador` (`id_utilizador`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Índices para tabela `login_tentativas`
--
ALTER TABLE `login_tentativas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `palestras`
--
ALTER TABLE `palestras`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `informacoes`
--
ALTER TABLE `informacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_tentativas`
--
ALTER TABLE `login_tentativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `palestras`
--
ALTER TABLE `palestras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  ADD CONSTRAINT `inscricoes_eventos_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `inscricoes_eventos_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
