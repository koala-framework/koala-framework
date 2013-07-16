<?php
class Kwf_Component_Cache_CrossPageClearCacheComponentLink_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCacheComponentLink_Page1_Component',
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CrossPageClearCacheComponentLink_Page2_Component',
        );

        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_CrossPageClearCacheComponentLink_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        return $ret;
    }
}
