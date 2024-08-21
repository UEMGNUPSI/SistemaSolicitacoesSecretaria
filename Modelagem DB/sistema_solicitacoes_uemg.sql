-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/08/2024 às 20:08
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_solicitacoes_uemg`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administrador`
--

CREATE TABLE `administrador` (
  `idadm` int(10) UNSIGNED NOT NULL,
  `nome_adm` varchar(50) NOT NULL,
  `cpf_adm` varchar(20) NOT NULL,
  `endereco_adm` varchar(80) NOT NULL,
  `cidade_adm` varchar(32) NOT NULL,
  `estado_adm` varchar(20) NOT NULL,
  `telefone_adm` varchar(15) NOT NULL,
  `senha_adm` varchar(255) NOT NULL,
  `status_adm` int(11) NOT NULL,
  `tp_u_idtpu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Despejando dados para a tabela `administrador`
--

INSERT INTO `administrador` (`idadm`, `nome_adm`, `cpf_adm`, `endereco_adm`, `cidade_adm`, `estado_adm`, `telefone_adm`, `senha_adm`, `status_adm`, `tp_u_idtpu`) VALUES
(2, 'Davi Vizicato', '2147483647', 'Rua Silva', 'Frutal', 'Mg', '1799239232', '12312321', 1, 2),
(3, 'Igor', 'admin', '456', '46456', '456456', '6456', 'admin', 1, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `aluno`
--

CREATE TABLE `aluno` (
  `idalu` int(11) NOT NULL,
  `nome_alu` varchar(50) NOT NULL,
  `cpf_alu` varchar(20) NOT NULL,
  `ra_alu` varchar(20) NOT NULL,
  `email_alu` varchar(100) NOT NULL,
  `celular_alu` varchar(15) NOT NULL,
  `turno_alu` varchar(10) NOT NULL,
  `status_alu` int(11) NOT NULL,
  `senha_alu` varchar(255) NOT NULL,
  `curso_idcur` int(11) NOT NULL,
  `tp_u_idtpu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Despejando dados para a tabela `aluno`
--

INSERT INTO `aluno` (`idalu`, `nome_alu`, `cpf_alu`, `ra_alu`, `email_alu`, `celular_alu`, `turno_alu`, `status_alu`, `senha_alu`, `curso_idcur`, `tp_u_idtpu`) VALUES
(33, 'Igor', 'igor', '56', '544645', '6656', 'integral', 0, '$2y$10$HJGTgOz9ZTLi1gt7MVWuiOQ.9VHz/vehdZUpCl/ECdp4XX03VehJm', 1, 1),
(35, 'teste', '11', '11', '111', '11', 'diurno', 1, '$2y$10$qLiUdILLB2NsuSFACkDRHu6fT1jJPRvyiGUYnLPufrXHS3Px2sVYy', 2, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `analise`
--

CREATE TABLE `analise` (
  `idana` int(11) NOT NULL,
  `data_conc_ana` date NOT NULL,
  `justificativa_ana` varchar(100) NOT NULL,
  `resultado_ana` varchar(9) NOT NULL,
  `encaminhamento_idenc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `coordenador`
--

CREATE TABLE `coordenador` (
  `idcrd` int(10) UNSIGNED NOT NULL,
  `nome_crd` varchar(50) NOT NULL,
  `cpf_crd` varchar(20) NOT NULL,
  `senha_crd` varchar(255) NOT NULL,
  `status_crd` int(11) NOT NULL,
  `masp_crd` int(11) NOT NULL,
  `curso_idcur` int(11) NOT NULL,
  `tp_u_idtpu` int(11) NOT NULL,
  `telefone_crd` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Despejando dados para a tabela `coordenador`
--

INSERT INTO `coordenador` (`idcrd`, `nome_crd`, `cpf_crd`, `senha_crd`, `status_crd`, `masp_crd`, `curso_idcur`, `tp_u_idtpu`, `telefone_crd`) VALUES
(2, 'Igor', '34345', 'senha', 1, 656, 2, 3, '17981696381');

-- --------------------------------------------------------

--
-- Estrutura para tabela `curso`
--

CREATE TABLE `curso` (
  `idcur` int(11) NOT NULL,
  `nome_cur` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Despejando dados para a tabela `curso`
--

INSERT INTO `curso` (`idcur`, `nome_cur`) VALUES
(1, 'Sistemas de Informação'),
(2, 'Direito'),
(3, 'Geografia'),
(4, 'Engenharia de Produção'),
(5, 'Administração'),
(6, 'Jornalismo'),
(7, 'Engenharia Agronômica'),
(8, 'Engenharia de Alimentos'),
(10, 'Publicidade e Propaganda');

-- --------------------------------------------------------

--
-- Estrutura para tabela `encaminhamento`
--

CREATE TABLE `encaminhamento` (
  `idenc` int(11) NOT NULL,
  `data_enc` date DEFAULT NULL,
  `data_retorno_enc` date DEFAULT NULL,
  `solicitação_idsol` int(11) NOT NULL,
  `administrador_idadm` int(10) UNSIGNED NOT NULL,
  `coordenador_idcrd` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacao`
--

CREATE TABLE `solicitacao` (
  `idsol` int(11) NOT NULL,
  `nome_curso_sol` varchar(30) NOT NULL,
  `periodo_alu_sol` int(3) NOT NULL,
  `solicitacao` varchar(255) NOT NULL,
  `justificativa_sol` varchar(255) NOT NULL,
  `status_sol` varchar(10) NOT NULL,
  `anexo_sol` varchar(255) NOT NULL,
  `tipo_sol` varchar(100) NOT NULL,
  `curso_idcur` int(11) NOT NULL,
  `aluno_idalu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tp_u`
--

CREATE TABLE `tp_u` (
  `idtpu` int(11) NOT NULL,
  `descricao_tpu` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Despejando dados para a tabela `tp_u`
--

INSERT INTO `tp_u` (`idtpu`, `descricao_tpu`) VALUES
(1, 'aluno'),
(2, 'administrador'),
(3, 'coordenador');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`idadm`),
  ADD KEY `fk_administrador_tp_u1` (`tp_u_idtpu`);

--
-- Índices de tabela `aluno`
--
ALTER TABLE `aluno`
  ADD PRIMARY KEY (`idalu`),
  ADD KEY `fk_aluno_curso1` (`curso_idcur`),
  ADD KEY `fk_aluno_tp_u1` (`tp_u_idtpu`);

--
-- Índices de tabela `analise`
--
ALTER TABLE `analise`
  ADD PRIMARY KEY (`idana`),
  ADD KEY `fk_analise_encaminhamento1` (`encaminhamento_idenc`);

--
-- Índices de tabela `coordenador`
--
ALTER TABLE `coordenador`
  ADD PRIMARY KEY (`idcrd`),
  ADD KEY `fk_coordenador_curso1` (`curso_idcur`),
  ADD KEY `fk_coordenador_tp_u1` (`tp_u_idtpu`);

--
-- Índices de tabela `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`idcur`);

--
-- Índices de tabela `encaminhamento`
--
ALTER TABLE `encaminhamento`
  ADD PRIMARY KEY (`idenc`),
  ADD KEY `fk_encaminhamento_solicitação1` (`solicitação_idsol`),
  ADD KEY `fk_encaminhamento_administrador1` (`administrador_idadm`),
  ADD KEY `fk_encaminhamento_coordenador1` (`coordenador_idcrd`);

--
-- Índices de tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  ADD PRIMARY KEY (`idsol`),
  ADD KEY `fk_solicitação_curso1` (`curso_idcur`),
  ADD KEY `fk_solicitação_aluno1` (`aluno_idalu`);

--
-- Índices de tabela `tp_u`
--
ALTER TABLE `tp_u`
  ADD PRIMARY KEY (`idtpu`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administrador`
--
ALTER TABLE `administrador`
  MODIFY `idadm` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `aluno`
--
ALTER TABLE `aluno`
  MODIFY `idalu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `analise`
--
ALTER TABLE `analise`
  MODIFY `idana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coordenador`
--
ALTER TABLE `coordenador`
  MODIFY `idcrd` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `curso`
--
ALTER TABLE `curso`
  MODIFY `idcur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `encaminhamento`
--
ALTER TABLE `encaminhamento`
  MODIFY `idenc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  MODIFY `idsol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tp_u`
--
ALTER TABLE `tp_u`
  MODIFY `idtpu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `fk_administrador_tp_u1` FOREIGN KEY (`tp_u_idtpu`) REFERENCES `tp_u` (`idtpu`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `aluno`
--
ALTER TABLE `aluno`
  ADD CONSTRAINT `fk_aluno_curso1` FOREIGN KEY (`curso_idcur`) REFERENCES `curso` (`idcur`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_aluno_tp_u1` FOREIGN KEY (`tp_u_idtpu`) REFERENCES `tp_u` (`idtpu`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `analise`
--
ALTER TABLE `analise`
  ADD CONSTRAINT `fk_analise_encaminhamento1` FOREIGN KEY (`encaminhamento_idenc`) REFERENCES `encaminhamento` (`idenc`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `coordenador`
--
ALTER TABLE `coordenador`
  ADD CONSTRAINT `fk_coordenador_curso1` FOREIGN KEY (`curso_idcur`) REFERENCES `curso` (`idcur`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_coordenador_tp_u1` FOREIGN KEY (`tp_u_idtpu`) REFERENCES `tp_u` (`idtpu`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `encaminhamento`
--
ALTER TABLE `encaminhamento`
  ADD CONSTRAINT `fk_encaminhamento_administrador1` FOREIGN KEY (`administrador_idadm`) REFERENCES `administrador` (`idadm`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_encaminhamento_coordenador1` FOREIGN KEY (`coordenador_idcrd`) REFERENCES `coordenador` (`idcrd`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_encaminhamento_solicitação1` FOREIGN KEY (`solicitação_idsol`) REFERENCES `solicitacao` (`idsol`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `solicitacao`
--
ALTER TABLE `solicitacao`
  ADD CONSTRAINT `fk_solicitação_aluno1` FOREIGN KEY (`aluno_idalu`) REFERENCES `aluno` (`idalu`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_solicitação_curso1` FOREIGN KEY (`curso_idcur`) REFERENCES `curso` (`idcur`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
