<?php
class Kwf_Component_Cache_CrossPageClearCache_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCache_Page1_Component',
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCache_Page2_Component',
        );
        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCache_Page3_Component',
        );
        $ret['generators']['includedPage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCache_IncludedPage_Component',
        );
        $ret['generators']['includedComponent'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCache_IncludedComponent_Component',
        );
        return $ret;
    }
}
