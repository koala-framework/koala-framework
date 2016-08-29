<?php
class Kwf_Component_ContentWidthBox_Page_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['pageWithEmptyBox'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'pageWithEmptyBox',
            'component' => 'Kwf_Component_ContentWidthBox_PageWithEmptyBox_Component'
        );
        return $ret;
    }
}
