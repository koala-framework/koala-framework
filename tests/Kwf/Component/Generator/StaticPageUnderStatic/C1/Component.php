<?php
class Kwf_Component_Generator_StaticPageUnderStatic_C1_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Abstract',
            'name' => 'page'
        );
        return $ret;
    }
}
