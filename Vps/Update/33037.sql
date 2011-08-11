#tags: vpc
-- phpMyAdmin SQL Dump
-- version 2.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. November 2010 um 14:38
-- Server Version: 5.0.51
-- PHP-Version: 5.2.6-1+lenny9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `ingenieurbueros_franz`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_component`
--

DROP TABLE IF EXISTS `cache_component`;
CREATE TABLE IF NOT EXISTS `cache_component` (
  `component_id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `page_id` varchar(255) character set latin1 collate latin1_general_ci default NULL,
  `db_id` varchar(255) character set latin1 collate latin1_general_ci default NULL,
  `component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `type` enum('component','master','partials','partial','mail','componentLink') character set latin1 collate latin1_general_ci NOT NULL,
  `value` varchar(20) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Bei Partial partialId oder bei master component_id zu der das master gehÃ¶rt',
  `expire` int(11) default NULL,
  `deleted` smallint(1) NOT NULL default '0',
  `content` longtext NOT NULL,
  PRIMARY KEY  (`component_id`,`type`,`value`),
  KEY `page_id` (`page_id`),
  KEY `component_class` (`component_class`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_componentpreload`
--

DROP TABLE IF EXISTS `cache_componentpreload`;
CREATE TABLE IF NOT EXISTS `cache_componentpreload` (
  `page_id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `preload_component_id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `preload_type` varchar(20) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`page_id`,`preload_component_id`,`preload_type`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_component_meta_chained`
--

DROP TABLE IF EXISTS `cache_component_meta_chained`;
CREATE TABLE IF NOT EXISTS `cache_component_meta_chained` (
  `source_component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`source_component_class`,`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_component_meta_component`
--

DROP TABLE IF EXISTS `cache_component_meta_component`;
CREATE TABLE IF NOT EXISTS `cache_component_meta_component` (
  `db_id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL default '',
  `component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `target_db_id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `meta_class` varchar(255) NOT NULL,
  PRIMARY KEY  (`db_id`,`component_class`,`target_db_id`,`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_component_meta_model`
--

DROP TABLE IF EXISTS `cache_component_meta_model`;
CREATE TABLE IF NOT EXISTS `cache_component_meta_model` (
  `model` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `pattern` varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '',
  `meta_class` varchar(100) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`component_class`,`pattern`,`meta_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_component_meta_row`
--

DROP TABLE IF EXISTS `cache_component_meta_row`;
CREATE TABLE IF NOT EXISTS `cache_component_meta_row` (
  `model` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `column` varchar(60) character set latin1 collate latin1_general_ci NOT NULL,
  `value` varchar(100) character set latin1 collate latin1_general_ci NOT NULL,
  `component_id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `meta_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`column`,`value`,`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
