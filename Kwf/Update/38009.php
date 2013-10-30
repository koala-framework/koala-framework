<?php
class Kwf_Update_38009 extends Kwf_Update
{
    protected $_tags = array('kwc');
    public function update()
    {
        if (Kwf_Cache_Simple::getMemcache()) {
            Kwf_Cache_Simple::getMemcache()->flush();
        }
        Kwf_Registry::get('db')->query('TRUNCATE cache_component');
        Kwf_Registry::get('db')->query('ALTER TABLE `cache_component` ADD `microtime` BIGINT( 14 ) NOT NULL AFTER `value`');
    }
}
