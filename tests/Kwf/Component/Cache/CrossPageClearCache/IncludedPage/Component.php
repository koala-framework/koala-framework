<?php
class Kwf_Component_Cache_CrossPageClearCache_IncludedPage_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_CrossPageClearCache_IncludedPage_Model';
        return $ret;
    }
}
