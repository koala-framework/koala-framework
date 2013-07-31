<?php
class Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Child_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Child_Model';
        return $ret;
    }
}
