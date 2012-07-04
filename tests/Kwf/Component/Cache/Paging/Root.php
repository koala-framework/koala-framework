<?php
// root is needed that dir is a page (paging component needs url)
class Kwf_Component_Cache_Paging_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['dir'] = array(
            'component' => 'Kwf_Component_Cache_Paging_Directory_Component',
            'class' => 'Kwf_Component_Generator_Page_Static'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
