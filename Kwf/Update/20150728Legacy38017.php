<?php
class Kwf_Update_20150728Legacy38017 extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        //clear all view caches, required for 7a36ae58a
        Kwf_Cache_Simple::getMemcache()->flush();
        Kwf_Registry::get('db')->query('TRUNCATE cache_component');
        Kwf_Registry::get('db')->query('TRUNCATE cache_component_includes');
    }
}
