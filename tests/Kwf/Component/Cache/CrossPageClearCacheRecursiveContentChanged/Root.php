<?php
class Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_StaticSelect',
            'component' => array(
                'parentContent' => 'Kwc_Basic_ParentContent_Component',
                'box' => 'Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_Box_Component'
            ),
            'model' => 'Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_StaticSelectModel',
            'inherit' => true
        );

        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );

        return $ret;
    }
}
