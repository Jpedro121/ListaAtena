-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08-Jun-2025 às 01:22
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
-- Estrutura da tabela `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `token_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `id_organizador` int(11) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `data` date NOT NULL,
  `hora` time DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `local` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `tipo` enum('evento','palestra') DEFAULT 'evento',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `id_organizador`, `titulo`, `data`, `hora`, `descricao`, `local`, `imagem`, `tipo`, `criado_em`) VALUES
(2, 10, 'Saude Mental', '2025-06-09', '10:05:00', 'Vamos ter uma particapao de uma mulher gloriosa', 'Auditório Grande', NULL, 'palestra', '2025-06-07 23:11:14');

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

--
-- Extraindo dados da tabela `login_tentativas`
--

INSERT INTO `login_tentativas` (`id`, `username`, `sucesso`, `ip`, `data_hora`) VALUES
(1, 'admin', 0, '::1', '2025-06-07 14:59:21'),
(10, 'jaoantun3s', 1, '::1', '2025-06-07 15:37:10'),
(11, 'jaoantun3s', 1, '::1', '2025-06-07 15:43:06'),
(12, 'jaoantun3s', 1, '::1', '2025-06-07 16:04:06'),
(13, 'jaoantun3s', 1, '::1', '2025-06-07 16:04:51'),
(14, 'jaoantun3s', 1, '::1', '2025-06-07 16:12:51'),
(15, 'jaoantun3s', 0, '::1', '2025-06-07 16:13:36'),
(16, 'jaoantun3s', 1, '::1', '2025-06-07 16:13:41'),
(17, 'jaoantun3s', 1, '::1', '2025-06-07 16:49:53'),
(18, 'jaoantun3s', 1, '::1', '2025-06-07 16:50:10'),
(19, 'jaoantun3s', 1, '::1', '2025-06-07 16:54:54'),
(20, 'jaoantun3s', 1, '::1', '2025-06-07 17:03:24'),
(21, 'jaoantun3s', 1, '::1', '2025-06-07 17:18:35'),
(22, 'jaoantun3s', 1, '::1', '2025-06-07 17:21:20'),
(23, 'jaoantun3s', 1, '192.168.1.66', '2025-06-07 17:54:57'),
(24, 'jaoantun3s', 1, '::1', '2025-06-07 17:58:00'),
(25, 'admin', 0, '::1', '2025-06-07 18:27:56'),
(26, 'admin', 0, '::1', '2025-06-07 18:35:35'),
(27, 'admin', 0, '::1', '2025-06-07 18:35:42'),
(28, 'admin', 0, '::1', '2025-06-07 18:37:37'),
(29, 'admin', 0, '::1', '2025-06-07 18:40:40'),
(30, 'admin', 1, '::1', '2025-06-07 18:43:22'),
(31, 'admin', 1, '::1', '2025-06-07 18:54:08'),
(32, 'jaoantun3s', 1, '::1', '2025-06-07 21:25:00'),
(33, 'admin', 1, '::1', '2025-06-07 21:48:07'),
(34, 'admin', 1, '::1', '2025-06-07 22:45:46'),
(35, 'jaoantun3s', 1, '::1', '2025-06-07 22:48:24'),
(36, 'admin', 0, '::1', '2025-06-07 22:49:28'),
(37, 'admin', 0, '::1', '2025-06-07 22:49:33'),
(38, 'admin', 1, '::1', '2025-06-07 22:49:37'),
(39, 'jaoantun3s', 1, '::1', '2025-06-07 22:51:03'),
(40, 'admin', 1, '::1', '2025-06-07 23:22:24'),
(41, 'jaoantun3s', 1, '::1', '2025-06-07 23:25:28'),
(42, 'admin', 1, '::1', '2025-06-07 23:25:47');

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
-- Estrutura da tabela `registo_logs`
--

CREATE TABLE `registo_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `registo_logs`
--

INSERT INTO `registo_logs` (`id`, `user_id`, `ip`, `data_registo`) VALUES
(3, 7, '::1', '2025-06-07 14:34:27');

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
  `token_ativacao` varchar(64) DEFAULT NULL,
  `tipo` enum('admin','aluno') DEFAULT 'aluno',
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 0,
  `tentativas_login` int(11) DEFAULT 0,
  `bloqueado_ate` datetime DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `username`, `senha`, `token_ativacao`, `tipo`, `data_registo`, `ultimo_login`, `ativo`, `tentativas_login`, `bloqueado_ate`, `foto_perfil`) VALUES
(7, 'João Pedro Antunes', 'a25035@esjs-mafra.net', 'jaoantun3s', '$2y$10$eLCOIuziNbB05kJ/SWvMF.KROWTCXssaCWH51yg3BgOBqwsJ7iTKe', NULL, 'aluno', '2025-06-07 14:34:27', '2025-06-07 22:25:28', 1, 0, NULL, 'avatar_7_1749314943.jpg'),
(10, 'Administrador', 'admin@gmail.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 'admin', '2025-06-07 17:43:10', '2025-06-07 23:11:18', 1, 0, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Índices para tabela `registo_logs`
--
ALTER TABLE `registo_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT de tabela `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `palestras`
--
ALTER TABLE `palestras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `registo_logs`
--
ALTER TABLE `registo_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  ADD CONSTRAINT `inscricoes_eventos_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `inscricoes_eventos_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`);

--
-- Limitadores para a tabela `registo_logs`
--
ALTER TABLE `registo_logs`
  ADD CONSTRAINT `registo_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
