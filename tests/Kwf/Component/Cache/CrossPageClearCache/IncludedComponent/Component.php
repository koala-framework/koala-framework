<?php
class Kwf_Component_Cache_CrossPageClearCache_IncludedComponent_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Cache_CrossPageClearCache_IncludedComponent_Model';
        return $ret;
    }
}
