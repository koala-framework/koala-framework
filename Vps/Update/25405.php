<?php
class Vps_Update_25283 extends Vps_Update
{
    public function update()
    {
        $sql = "ALTER TABLE `cache_component_meta` CHANGE `type` `type` ENUM( 'cacheId', 'callback', 'componentClass', 'static' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL";
        Zend_Registry::get('db')->query($sql);
    }
}
