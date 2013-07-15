<?php
class Kwf_Component_Cache_CrossPageClearCacheChild_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCacheChild_Page1_Component',
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCacheChild_Page2_Component',
        );
        $ret['generators']['includedPage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Component',
        );
        return $ret;
    }
}
