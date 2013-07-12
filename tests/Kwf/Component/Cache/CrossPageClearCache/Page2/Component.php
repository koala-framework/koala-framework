<?php
class Kwf_Component_Cache_CrossPageClearCache_Page2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_CrossPageClearCache_Page2_Model';
        return $ret;
    }
}
