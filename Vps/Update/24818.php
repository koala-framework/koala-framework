<?php
class Vps_Update_24818 extends Vps_Update
{
    protected function _init()
    {
        $sql = "ALTER TABLE `cache_component_meta` ADD `callback` VARCHAR( 255 ) NOT NULL AFTER `cache_id`";
        Zend_Registry::get('db')->query($sql);
    }
}
