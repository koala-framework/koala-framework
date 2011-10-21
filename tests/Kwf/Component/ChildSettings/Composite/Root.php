<?php
class Kwf_Component_ChildSettings_Composite_Root extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['child1'] = 'Kwc_Basic_Empty_Component';
        $ret['generators']['child']['component']['child2'] = 'Kwc_Basic_Empty_Component';

        $ret['childSettings']['child_child1'] = array(
            'componentName' => 'child1name'
        );
        $ret['childSettings']['child_child2'] = array(
            'componentName' => 'child2name'
        );
        return $ret;
    }
}
