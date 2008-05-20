<?php
class Vpc_News_Directory_TreeCache extends Vpc_News_List_Abstract_TreeCache
{
    protected $_additionalTreeCaches = array(
        'Vpc_News_Directory_TreeCacheDetail',
        'Vpc_News_Directory_TreeCacheMenuBox',
        'Vpc_News_List_Abstract_TreeCacheFeed'
    );
    protected function _init()
    {
        foreach ($this->_getSetting('childComponentClasses') as $k=>$c) {
            if (Vpc_Abstract::hasSetting($c, 'ownTreeCache')) {
                $this->_additionalTreeCaches[] = Vpc_Abstract::getSetting($c,
                                                                'ownTreeCache');
            }
        }
        parent::_init();
    }
}
