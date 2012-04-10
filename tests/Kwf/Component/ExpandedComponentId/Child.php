<?php
class Kwf_Component_ExpandedComponentId_Child extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['bar'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_ExpandedComponentId_Child2'
        );
        return $ret;
    }
}
?>