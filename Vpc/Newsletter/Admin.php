<?php
class Vpc_Newsletter_Admin extends Vpc_Directories_Item_Directory_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    protected function _getContentClass()
    {
        return Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['items']['idSeparator'] = '_';
        return $ret;
    }
    
    public function setup()
    {
        $sql = "
            DROP TABLE IF EXISTS `vpc_newsletter`;
            CREATE TABLE IF NOT EXISTS `vpc_newsletter` (
              `id` smallint(6) NOT NULL auto_increment,
              `component_id` varchar(255) default NULL,
              `create_date` datetime NOT NULL,
              `status` enum('start','pause','stop','sending','finished') default NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
            
            DROP TABLE IF EXISTS `vpc_newsletter_log`;
            CREATE TABLE IF NOT EXISTS `vpc_newsletter_log` (
              `id` int(11) NOT NULL auto_increment,
              `newsletter_id` smallint(6) NOT NULL,
              `start` datetime NOT NULL,
              `stop` datetime NOT NULL,
              `count` smallint(6) NOT NULL,
              `countErrors` smallint(6) NOT NULL,
              PRIMARY KEY  (`id`),
              KEY `newsletter_id` (`newsletter_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
            
            DROP TABLE IF EXISTS `vpc_newsletter_queue`;
            CREATE TABLE IF NOT EXISTS `vpc_newsletter_queue` (
              `id` int(11) NOT NULL auto_increment,
              `newsletter_id` smallint(6) NOT NULL,
              `recipient_model` varchar(255) NOT NULL,
              `recipient_id` varchar(255) NOT NULL,
              `searchtext` varchar(255) NOT NULL,
              `status` enum('queued','sending','userNotFound','sent','sendingError') NOT NULL default 'queued',
              `sent_date` timestamp NULL default NULL,
              PRIMARY KEY  (`id`),
              UNIQUE KEY `newsletter_id_2` (`newsletter_id`,`recipient_model`,`recipient_id`),
              KEY `newsletter_id` (`newsletter_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
            
            ALTER TABLE `vpc_newsletter_queue`
              ADD CONSTRAINT `vpc_newsletter_queue_ibfk_1` FOREIGN KEY (`newsletter_id`) REFERENCES `vpc_newsletter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ";
        Vps_Registry::get('db')->query($sql);
    }
}
