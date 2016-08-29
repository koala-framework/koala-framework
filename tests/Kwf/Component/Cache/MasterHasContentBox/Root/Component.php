<?php
class Kwf_Component_Cache_MasterHasContentBox_Root_Component extends Kwc_Root_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['generators']['box1'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'box1' => 'Kwf_Component_Cache_MasterHasContentBox_Box_Component',
            ),
            'inherit' => true,
        );
        $ret['generators']['box2'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'box2' => 'Kwf_Component_Cache_MasterHasContentBox_Box_Component',
            ),
            'inherit' => true,
            'unique' => true,
        );
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'name' => 'page1'
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'name' => 'page2'
        );
        return $ret;
    }
}
