-- phpMyAdmin SQL Dump
-- version 3.1.2deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 08 Juillet 2009 à 19:24
-- Version du serveur: 5.0.75
-- Version de PHP: 5.2.6-3ubuntu4.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `framy`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `cat_order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `category`
--

INSERT INTO `category` (`id`, `name`, `cat_order`) VALUES
(1, 'Download', 1),
(2, 'User''s forums', 1);

-- --------------------------------------------------------

--
-- Structure de la table `forum`
--

CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `forum_order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Contenu de la table `forum`
--

INSERT INTO `forum` (`id`, `category_id`, `name`, `forum_order`) VALUES
(1, 1, 'lastest download', 0),
(2, 2, 'fofo de user1', 0),
(3, 2, 'le fofo du user2', 0);

-- --------------------------------------------------------

--
-- Structure de la table `forum_moderator`
--

CREATE TABLE IF NOT EXISTS `forum_moderator` (
  `forum_id` int(10) unsigned NOT NULL,
  `moderator_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `forum_moderator`
--

INSERT INTO `forum_moderator` (`forum_id`, `moderator_id`) VALUES
(2, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `moderator`
--

CREATE TABLE IF NOT EXISTS `moderator` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `moderator`
--

INSERT INTO `moderator` (`id`, `user_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `post`
--


-- --------------------------------------------------------

--
-- Structure de la table `subject`
--

CREATE TABLE IF NOT EXISTS `subject` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `forum_id` int(10) unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `subject`
--


-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `login`) VALUES
(1, 'user1'),
(2, 'user2'),
(3, 'user3');
