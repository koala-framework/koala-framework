
CREATE TABLE `cards_firstname` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wrapper_id` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wrapper_id` (`wrapper_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `cards_lastname` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wrapper_id` int(10) unsigned NOT NULL,
  `lastname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wrapper_id` (`wrapper_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `cards_wrapper` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `wrappertext` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `cards_wrapper` (`id` ,`type` ,`wrappertext` ,`comment`) VALUES (NULL , 'siblast', 'wtext', 'wcomm');
INSERT INTO `cards_lastname` (`id` ,`wrapper_id` ,`lastname`) VALUES (NULL , '1', 'lasttest');
