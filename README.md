# getRepos
Testing app

-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databáze: `getrepos`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vyhledavani`
--

CREATE TABLE IF NOT EXISTS `vyhledavani` (
  `ID` int(8) NOT NULL AUTO_INCREMENT,
  `IP` varchar(39) COLLATE utf8_czech_ci NOT NULL,
  `Dotaz` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `Datum` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;
