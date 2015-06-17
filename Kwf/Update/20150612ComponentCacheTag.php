<?php
class Kwf_Update_20150612ComponentCacheTag extends Kwf_Update
{
    protected $_tags = array('kwc');


    public function update()
    {
        $db = Kwf_Registry::get('db');
        $db->query("TRUNCATE `cache_component`;");
        $db->query("ALTER TABLE `cache_component` ADD  `tag` VARCHAR( 255 ) CHARACTER SET ASCII COLLATE ascii_bin NOT NULL AFTER  `value`;");
        $db->query("ALTER TABLE `cache_component` ADD INDEX  `tag` (  `tag` );");

        //because of TRUNCATE above we need to clear the whole memory cache
        Kwf_Component_Cache_Memory::getInstance()->_clean();
    }
}
