DROP TABLE IF EXISTS `snote_language_variables`;
CREATE TABLE `snote_language_variables` (
  `lang_code` char(2) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`lang_code`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `snote_languages`;
CREATE TABLE `snote_languages` (
  `lang_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` char(2) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT 'A',
  `country_code` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `lang_code` (`lang_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;