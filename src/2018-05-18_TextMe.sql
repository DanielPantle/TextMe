-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 18. Mai 2018 um 16:31
-- Server-Version: 10.1.31-MariaDB
-- PHP-Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `textme`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
  `cid` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `timeadded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timemodified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `chat`
--

INSERT INTO `chat` (`cid`, `name`, `timeadded`, `timemodified`) VALUES
(1, 'juleflo', '2018-05-03 12:14:24', '2018-05-03 12:14:24'),
(2, 'juledaniel', '2018-05-03 12:14:24', '2018-05-03 12:14:24'),
(3, 'julechristian', '2018-05-03 12:14:24', '2018-05-03 12:14:24'),
(4, 'juledodotim', '2018-05-03 12:14:24', '2018-05-03 12:14:24');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message`
--

CREATE TABLE `message` (
  `mid` int(11) NOT NULL,
  `uiicid` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `timeadded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timemodified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `message`
--

INSERT INTO `message` (`mid`, `uiicid`, `message`, `timeadded`, `timemodified`) VALUES
(1, 1, 'Hi Flo!', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(2, 5, 'Hi Julia...', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(3, 2, 'hallo daniel', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(4, 6, 'hi julia', '2018-05-03 12:30:17', '2018-05-18 13:11:26'),
(5, 3, 'hi chris:D', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(6, 7, 'hi jule', '2018-05-03 12:30:17', '2018-05-18 13:11:39'),
(7, 7, 'wie gehts?', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(8, 4, 'hi gruppe', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(9, 8, 'guten tag gruppe', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(10, 9, 'ahoi pineapples!!!', '2018-05-03 12:30:17', '2018-05-03 12:30:17'),
(11, 2, 'Hi', '2018-05-18 13:11:58', '2018-05-18 13:11:58'),
(12, 4, 'Hi', '2018-05-18 13:48:20', '2018-05-18 13:48:20'),
(13, 1, 'du freust dich aber', '2018-05-18 14:19:06', '2018-05-18 14:19:06');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `uid` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `timeadded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timemodified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`uid`, `name`, `mail`, `password`, `timeadded`, `timemodified`) VALUES
(1, 'Christian', 'christian@mail.de', '$2y$10$pKoFXWyXkKvKSsEjZnljXeHSSm9qZ4vSYNQmonlvBwA4lkrRY0c52', '2018-05-03 11:25:19', '2018-05-07 09:44:37'),
(2, 'Dodo', 'dodo@mail.de', '$2y$10$pKoFXWyXkKvKSsEjZnljXeHSSm9qZ4vSYNQmonlvBwA4lkrRY0c52', '2018-05-03 11:25:19', '2018-05-07 09:44:40'),
(3, 'Daniel', 'daniel@mail.de', '$2y$10$pKoFXWyXkKvKSsEjZnljXeHSSm9qZ4vSYNQmonlvBwA4lkrRY0c52', '2018-05-03 11:25:19', '2018-05-07 09:44:42'),
(4, 'Flo', 'flo@mail.de', '$2y$10$pKoFXWyXkKvKSsEjZnljXeHSSm9qZ4vSYNQmonlvBwA4lkrRY0c52', '2018-05-03 11:25:19', '2018-05-07 09:44:43'),
(5, 'Jule', 'jule@mail.de', '$2y$10$pKoFXWyXkKvKSsEjZnljXeHSSm9qZ4vSYNQmonlvBwA4lkrRY0c52', '2018-05-03 11:25:19', '2018-05-07 09:44:45'),
(6, 'tim', 'tim@mail.de', '$2y$10$pKoFXWyXkKvKSsEjZnljXeHSSm9qZ4vSYNQmonlvBwA4lkrRY0c52', '2018-05-03 11:40:06', '2018-05-07 09:44:47');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_is_in_chat`
--

CREATE TABLE `user_is_in_chat` (
  `uiicid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `timeadded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timemodified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `user_is_in_chat`
--

INSERT INTO `user_is_in_chat` (`uiicid`, `cid`, `uid`, `timeadded`, `timemodified`, `deleted`) VALUES
(1, 1, 5, '2018-05-03 12:20:29', '2018-05-03 12:20:29', 0),
(2, 2, 5, '2018-05-03 12:20:29', '2018-05-03 12:20:29', 0),
(3, 3, 5, '2018-05-03 12:20:29', '2018-05-03 12:20:29', 0),
(4, 4, 5, '2018-05-03 12:20:29', '2018-05-18 16:30:20', 0),
(5, 1, 4, '2018-05-03 12:22:21', '2018-05-03 12:22:21', 0),
(6, 2, 3, '2018-05-03 12:22:21', '2018-05-03 12:22:21', 0),
(7, 3, 1, '2018-05-03 12:22:21', '2018-05-03 12:22:21', 0),
(8, 4, 6, '2018-05-03 12:22:21', '2018-05-03 12:22:21', 0),
(9, 4, 2, '2018-05-03 12:22:21', '2018-05-03 12:22:21', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `cid_UNIQUE` (`cid`);

--
-- Indizes für die Tabelle `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`mid`),
  ADD UNIQUE KEY `mid_UNIQUE` (`mid`),
  ADD KEY `uiicid_idx` (`uiicid`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uid_UNIQUE` (`uid`);

--
-- Indizes für die Tabelle `user_is_in_chat`
--
ALTER TABLE `user_is_in_chat`
  ADD PRIMARY KEY (`uiicid`),
  ADD UNIQUE KEY `id_UNIQUE` (`uiicid`),
  ADD KEY `cid_idx` (`cid`),
  ADD KEY `uid_idx` (`uid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `chat`
--
ALTER TABLE `chat`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `message`
--
ALTER TABLE `message`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `user_is_in_chat`
--
ALTER TABLE `user_is_in_chat`
  MODIFY `uiicid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `uiicid_fk_m` FOREIGN KEY (`uiicid`) REFERENCES `user_is_in_chat` (`uiicid`);

--
-- Constraints der Tabelle `user_is_in_chat`
--
ALTER TABLE `user_is_in_chat`
  ADD CONSTRAINT `cid_fk_uiic` FOREIGN KEY (`cid`) REFERENCES `chat` (`cid`),
  ADD CONSTRAINT `uid_fk_uiic` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
