<?php
class Vps_Update_32011 extends Vps_Update
{
    protected $_tags = array('pages');

    public function update()
    {
        parent::update();
        $db = Zend_Registry::get('db');
        $cc = Vps_Registry::get('config')->vpc->rootComponent;
        $config = $db->getconfig();
        $dbname = $config['dbname'];
        $keys = $db->fetchAll("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA='$dbname' AND table_name='vps_pages' AND column_name='parent_id'");
        foreach ($keys as $key) {
            $k = $key['CONSTRAINT_NAME'];
            $db->query("ALTER TABLE `vps_pages` DROP FOREIGN KEY `$k`;");

        }
        $db->query("ALTER TABLE `vps_pages` CHANGE `parent_id` `parent_id` VARCHAR( 255 ) NOT NULL");
        if (is_subclass_of($cc, 'Vpc_Root_DomainRoot_Component')) {
            $db->query("UPDATE vps_pages SET parent_id=CONCAT('root-', domain, '-', category) WHERE parent_id=''");
            $db->query("ALTER TABLE `vps_pages` DROP `category`;");
            $db->query("ALTER TABLE `vps_pages` DROP `domain`;");
        } else {
            $db->query("UPDATE vps_pages SET parent_id=CONCAT('root-', category) WHERE parent_id=''");
            $db->query("ALTER TABLE `vps_pages` DROP `category`;");
        }
        $db->query("SET foreign_key_checks = 1;");
    }
}
