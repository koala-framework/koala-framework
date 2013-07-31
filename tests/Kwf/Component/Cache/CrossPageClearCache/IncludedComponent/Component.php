<?php
class Kwf_Component_Cache_CrossPageClearCache_IncludedComponent_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_CrossPageClearCache_IncludedComponent_Model';
        return $ret;
    }
}
