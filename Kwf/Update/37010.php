<?php
class Kwf_Update_37010 extends Kwf_Update
{
    protected $_tags = array('kwc');
    public function update()
    {
        //also increase Kwf_Component_Cache_Memory::CACHE_VERSION
        Kwf_Registry::get('db')->query('TRUNCATE cache_component');
    }
}
