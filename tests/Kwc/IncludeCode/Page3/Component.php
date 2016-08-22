<?php
class Kwc_IncludeCode_Page3_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'component' => 'Kwc_IncludeCode_Page2_Child_Component',
            'class' => 'Kwf_Component_Generator_Static',
        );
        return $ret;
    }
}
