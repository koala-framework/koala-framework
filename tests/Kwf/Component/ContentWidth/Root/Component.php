<?php
class Kwf_Component_ContentWidth_Root_Component extends Kwc_Root_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['contentWidth'] = 800;
        $ret['contentWidthBoxSubtract'] = array();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'page',
            'component' => 'Kwf_Component_ContentWidth_Page_Component'
        );
        return $ret;
    }
}
