CREATE TABLE IF NOT EXISTS `vpc_mail` (
  `component_id` varchar(255) character set utf8 NOT NULL,
  `subject` varchar(255) character set utf8 NOT NULL,
  `from_email` varchar(255) character set utf8 NOT NULL,
  `from_name` varchar(255) character set utf8 NOT NULL,
  `reply_email` varchar(255) character set utf8 NOT NULL,
  PRIMARY KEY  (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

