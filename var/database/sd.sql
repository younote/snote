-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 31 2014 г., 19:28
-- Версия сервера: 5.6.12
-- Версия PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `aarefiev_stickdev`
--
CREATE DATABASE IF NOT EXISTS `aarefiev_stickdev` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `aarefiev_stickdev`;

-- --------------------------------------------------------

--
-- Структура таблицы `snote_languages`
--

CREATE TABLE IF NOT EXISTS `snote_languages` (
  `lang_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` char(2) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `country_code` char(2) NOT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `snote_languages`
--

INSERT INTO `snote_languages` (`lang_id`, `lang_code`, `name`, `status`, `country_code`) VALUES
(1, 'en', 'English', 'A', 'EN'),
(2, 'ru', 'Русский', 'A', 'RU');

-- --------------------------------------------------------

--
-- Структура таблицы `snote_language_variables`
--

CREATE TABLE IF NOT EXISTS `snote_language_variables` (
  `lang_code` char(2) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`lang_code`,`name`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `snote_language_variables`
--

INSERT INTO `snote_language_variables` (`lang_code`, `name`, `value`) VALUES
('en', 'welcome', 'Welcome'),
('ru', 'welcome', 'Добро пожаловать'),
('en', 'login', 'Login'),
('ru', 'login', 'Войти');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
