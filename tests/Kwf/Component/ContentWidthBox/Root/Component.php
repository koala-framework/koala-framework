<?php
class Kwf_Component_ContentWidthBox_Root_Component extends Kwc_Root_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['contentWidth'] = 800;
        $ret['contentWidthBoxSubtract'] = array(
            'testBox' => 100
        );
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'page',
            'component' => 'Kwf_Component_ContentWidthBox_Page_Component'
        );
        $ret['generators']['pageWithEmptyBox'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'pageWithEmptyBox',
            'component' => 'Kwf_Component_ContentWidthBox_PageWithEmptyBox_Component'
        );
        $ret['generators']['testBox'] = array(
            'component' => 'Kwf_Component_ContentWidthBox_Box_Component',
            'class' => 'Kwf_Component_Generator_Box_Static',
            'unique' => true,
            'inherit' => true
        );
        return $ret;
    }
}
