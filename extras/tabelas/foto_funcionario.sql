-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 09/10/2020 às 01:26
-- Versão do servidor: 5.7.26
-- Versão do PHP: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Banco de dados: `crud_vikings`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `foto_funcionario`
--

CREATE TABLE `foto_funcionario` (
  `id_registro` int(11) NOT NULL,
  `id_funcionario` int(11) DEFAULT NULL,
  `url` varchar(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `foto_funcionario`
--
ALTER TABLE `foto_funcionario`
  ADD PRIMARY KEY (`id_registro`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `foto_funcionario`
--
ALTER TABLE `foto_funcionario`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
