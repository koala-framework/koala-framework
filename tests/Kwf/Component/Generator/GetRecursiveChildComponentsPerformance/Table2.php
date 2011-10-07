<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table2 extends Kwf_Component_Generator_GetComponentByClassWithComponentId_Table
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => array(
                'table3' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table3',
                'empty'  => 'Kwc_Basic_Empty_Component'
            ),
            'model' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table2Model'
        );
        return $ret;
    }
}
