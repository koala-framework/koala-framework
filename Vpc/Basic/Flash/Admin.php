<?php
class Vpc_Basic_Flash_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['width'] = 'int(11) DEFAULT NULL';
        $fields['height'] = 'int(11) DEFAULT NULL';
        $fields['vps_upload_id_media'] = 'int(11) DEFAULT NULL';
        $this->createFormTable('vpc_basic_flash', $fields);

        $sql = "CREATE TABLE IF NOT EXISTS `vpc_basic_flash_vars` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `parent_id` int(10) unsigned NOT NULL,
            `key` varchar(255) default NULL,
            `value` varchar(255) default NULL,
            PRIMARY KEY  (`id`),
            KEY `parent_id` (`parent_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        Vps_Registry::get('db')->query($sql);
    }
}
