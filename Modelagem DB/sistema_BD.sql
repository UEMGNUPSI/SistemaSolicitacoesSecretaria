-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/07/2024 às 20:31
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `secretaria_uemg`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administrador`
--

CREATE TABLE `administrador` (
  `idadm` int(10) UNSIGNED NOT NULL,
  `nome_adm` varchar(50) NOT NULL,
  `cpf_adm` int(11) NOT NULL,
  `endereco_adm` varchar(80) NOT NULL,
  `cidade_adm` varchar(32) NOT NULL,
  `estado_adm` varchar(20) NOT NULL,
  `telefone_adm` int(11) NOT NULL,
  `senha_adm` varchar(20) NOT NULL,
  `status_adm` int(11) NOT NULL,
  `tp_u_idtpu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aluno`
--

CREATE TABLE `aluno` (
  `idalu` int(11) NOT NULL,
  `nome_alu` varchar(50) NOT NULL,
  `cpf_alu` int(11) NOT NULL,
  `ra_alu` int(11) NOT NULL,
  `email_alu` varchar(100) NOT NULL,
  `celular_alu` int(11) NOT NULL,
  `periodo_alu` int(11) NOT NULL,
  `turno_alu` varchar(5) NOT NULL,
  `status_alu` int(11) NOT NULL,
  `senha_alu` varchar(30) NOT NULL,
  `curso_idcur` int(11) NOT NULL,
  `tp_u_idtpu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

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
  `cpf_crd` int(11) NOT NULL,
  `senha_crd` varchar(30) NOT NULL,
  `status_crd` int(11) NOT NULL,
  `masp_crd` int(11) NOT NULL,
  `curso_idcur` int(11) NOT NULL,
  `tp_u_idtpu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curso`
--

CREATE TABLE `curso` (
  `idcur` int(11) NOT NULL,
  `nome_cur` varchar(23) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

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
-- Estrutura para tabela `solicitação`
--

CREATE TABLE `solicitação` (
  `idsol` int(11) NOT NULL,
  `nome_curso_sol` varchar(30) NOT NULL,
  `justificativa_sol` varchar(150) NOT NULL,
  `status_sol` varchar(10) NOT NULL,
  `anexo_sol` blob NOT NULL,
  `tipo_sol` varchar(30) NOT NULL,
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
-- Índices de tabela `solicitação`
--
ALTER TABLE `solicitação`
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
  MODIFY `idadm` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aluno`
--
ALTER TABLE `aluno`
  MODIFY `idalu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `analise`
--
ALTER TABLE `analise`
  MODIFY `idana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coordenador`
--
ALTER TABLE `coordenador`
  MODIFY `idcrd` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `curso`
--
ALTER TABLE `curso`
  MODIFY `idcur` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `encaminhamento`
--
ALTER TABLE `encaminhamento`
  MODIFY `idenc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitação`
--
ALTER TABLE `solicitação`
  MODIFY `idsol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tp_u`
--
ALTER TABLE `tp_u`
  MODIFY `idtpu` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `fk_encaminhamento_solicitação1` FOREIGN KEY (`solicitação_idsol`) REFERENCES `solicitação` (`idsol`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `solicitação`
--
ALTER TABLE `solicitação`
  ADD CONSTRAINT `fk_solicitação_aluno1` FOREIGN KEY (`aluno_idalu`) REFERENCES `aluno` (`idalu`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_solicitação_curso1` FOREIGN KEY (`curso_idcur`) REFERENCES `curso` (`idcur`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
