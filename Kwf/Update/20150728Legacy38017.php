<?php
class Kwf_Update_20150728Legacy38017 extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        //clear all view caches, required for 7a36ae58a
        Kwf_Component_Cache_Memory::getInstance()->_clean();
        Kwf_Registry::get('db')->query('TRUNCATE cache_component');
        Kwf_Registry::get('db')->query('TRUNCATE cache_component_includes');
    }
}
