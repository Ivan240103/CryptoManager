-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 03, 2022 alle 22:34
-- Versione del server: 10.4.24-MariaDB
-- Versione PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfoliodb`
--
CREATE DATABASE IF NOT EXISTS `portfoliodb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portfoliodb`;

-- --------------------------------------------------------

--
-- Struttura della tabella `assets`
--

CREATE TABLE `assets` (
  `idAsset` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `symbol` char(5) NOT NULL,
  `assetName` char(30) NOT NULL,
  `quantity` decimal(20,10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `assets`
--

INSERT INTO `assets` (`idAsset`, `user`, `symbol`, `assetName`, `quantity`) VALUES
(1, 4, 'BTC', 'Bitcoin', '1.0000000000'),
(1027, 4, 'ETH', 'Ethereum', '1.0000000000'),
(3635, 4, 'CRO', 'Cronos', '2.0000000000'),
(6538, 4, 'CRV', 'Curve DAO Token', '9.0000000000');

-- --------------------------------------------------------

--
-- Struttura della tabella `operations`
--

CREATE TABLE `operations` (
  `idOperation` int(11) NOT NULL,
  `orderType` char(4) NOT NULL,
  `price` decimal(20,10) NOT NULL,
  `quantity` decimal(20,10) NOT NULL,
  `idAsset` int(11) NOT NULL,
  `symbol` char(5) NOT NULL,
  `assetName` char(30) DEFAULT NULL,
  `execDate` date NOT NULL,
  `execTime` time NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `operations`
--

INSERT INTO `operations` (`idOperation`, `orderType`, `price`, `quantity`, `idAsset`, `symbol`, `assetName`, `execDate`, `execTime`, `user`) VALUES
(9, 'buy', '30000.0000000000', '1.0000000000', 1, 'BTC', 'Bitcoin', '2022-06-03', '21:02:00', 4),
(10, 'buy', '4000.0000000000', '2.0000000000', 1027, 'ETH', 'Ethereum', '2022-06-01', '12:00:00', 4),
(11, 'sell', '2300.0000000000', '1.0000000000', 1027, 'ETH', 'Ethereum', '2022-06-03', '21:04:00', 4),
(12, 'buy', '27.0000000000', '9.0000000000', 6538, 'CRV', 'Curve DAO Token', '2022-03-16', '15:30:00', 4),
(13, 'buy', '0.4000000000', '2.0000000000', 3635, 'CRO', 'Cronos', '2022-05-11', '09:00:00', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `idUser` int(11) NOT NULL,
  `name` char(30) DEFAULT NULL,
  `surname` char(30) DEFAULT NULL,
  `email` char(50) NOT NULL,
  `username` char(20) NOT NULL,
  `password` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`idUser`, `name`, `surname`, `email`, `username`, `password`) VALUES
(3, 'Utente', 'Admin', 'ivan.desimone.s@belluzzifioravanti.it', 'root', 'MqgGPA=='),
(4, 'Ivan', 'De Simone', 'ivan03ds@gmail.com', 'ivan', 'KbEIJg==');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`idAsset`,`user`),
  ADD KEY `asset-user` (`user`);

--
-- Indici per le tabelle `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`idOperation`),
  ADD KEY `user-ope` (`user`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUser`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `operations`
--
ALTER TABLE `operations`
  MODIFY `idOperation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `asset-user` FOREIGN KEY (`user`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `operations`
--
ALTER TABLE `operations`
  ADD CONSTRAINT `user-ope` FOREIGN KEY (`user`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
