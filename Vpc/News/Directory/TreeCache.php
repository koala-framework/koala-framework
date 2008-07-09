<?php
class Vpc_News_Directory_TreeCache extends Vpc_News_List_Abstract_TreeCache
{
    protected function _init()
    {
        if (!isset($this->_additionalTreeCaches['detail'])) {
            $this->_additionalTreeCaches['detail'] = 'Vpc_News_Directory_TreeCacheDetail';
        }
        $this->_additionalTreeCaches['menu'] = 'Vpc_News_Directory_TreeCacheMenuBox';
        $this->_additionalTreeCaches['feed'] = 'Vpc_News_List_Abstract_TreeCacheFeed';

        foreach ($this->_getSetting('childComponentClasses') as $k=>$c) {
            if (Vpc_Abstract::hasSetting($c, 'ownTreeCache')) {
                $this->_additionalTreeCaches[] = Vpc_Abstract::getSetting($c,
                                                                'ownTreeCache');
            }
        }
        parent::_init();
    }
}
