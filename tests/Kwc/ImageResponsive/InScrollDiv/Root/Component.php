<?php
class Kwc_ImageResponsive_InScrollDiv_Root_Component extends Kwc_Root_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['image1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_ImageResponsive_InScrollDiv_Components_Image_Component'
        );
        $ret['generators']['image2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_ImageResponsive_InScrollDiv_Components_Image_Component'
        );
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
