<?php
class Kwf_Component_ChildSettings_CompositeByChildSettings_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['first'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Abstract_Composite_Component'
        );

        $ret['childSettings']['first'] = array(
            'generators' => array(
                'child' => array(
                    'component' => array(
                        'second1' => 'Kwc_Basic_None_Component',
                        'second2' => 'Kwc_Basic_None_Component'
                    )
                )
            )
        );

        $ret['childSettings']['first.child_second1'] = array(
            'componentName' => 'second1name'
        );

        $ret['childSettings']['first.child_second2'] = array(
            'componentName' => 'second2name'
        );

        return $ret;
    }
}