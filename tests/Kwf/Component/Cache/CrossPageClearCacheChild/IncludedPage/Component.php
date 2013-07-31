<?php
class Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['child'] = 'Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Child_Component';
        return $ret;
    }
}
