<?php
class Kwc_Paging_Abstract_UseViewCachePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache
{
    public function useViewCache($renderer)
    {
        $cacheId = 'paging-' . $this->_componentId;
        $disableCacheParam = Kwf_Cache_Simple::fetch($cacheId, $success);
        if (!$success) {
            $disableCacheParam = null;
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->_componentId, array('ignoreVisible'=>true))
                ->parent->getComponent();
            if ($c instanceof Kwc_Directories_List_View_Component && $c->hasSearchForm()) {
                $disableCacheParam = $c->getSearchForm()->componentId.'-post';
            }
            Kwf_Cache_Simple::add($cacheId, $disableCacheParam);
        }
        $ret = true;
        if ($disableCacheParam) {
            $ret = !isset($_REQUEST[$disableCacheParam]);
        }
        return $ret;
    }
}
