<?php
class Kwf_Component_ExpandedComponentId_Child2 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>