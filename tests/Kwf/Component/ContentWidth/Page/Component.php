<?php
class Kwf_Component_ContentWidth_Page_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_ContentWidth_Page_Child_Component'
        );
        $ret['contentWidthSubtract'] = 10;
        return $ret;
    }
}
