<?php
class Kwf_Component_Cache_MenuStaticPage_Test_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'page',
            'component' => 'Kwc_Basic_Empty_Component',
            'showInMenu' => true
        );
        return $ret;
    }
}
