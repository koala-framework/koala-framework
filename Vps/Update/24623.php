<?php
class Vps_Update_24623 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'email'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'password'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'gender'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'title'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'firstname'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'lastname'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'webcode'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'created'
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropField(array(
            'table' => 'vps_users', 'field' => 'last_modified'
        ));
    }

    public function update()
    {
        parent::update();
        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `cache_users` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL,
  `deleted` tinyint(1) default NULL,
  `password` varchar(40) NOT NULL,
  `password_salt` varchar(10) NOT NULL,
  `gender` enum('','female','male') NOT NULL,
  `title` varchar(100) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `webcode` varchar(100) NOT NULL,
  `created` datetime default NULL,
  `logins` int(11) default NULL,
  `last_login` datetime default NULL,
  `last_modified` datetime NOT NULL,
  `locked` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email_2` (`email`,`webcode`,`deleted`),
  KEY `email` (`email`),
  KEY `webcode` (`webcode`),
  KEY `last_modified` (`last_modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    }
}
