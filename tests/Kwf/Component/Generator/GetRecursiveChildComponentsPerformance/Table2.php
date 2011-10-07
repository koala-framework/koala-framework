<?php
class Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table2 extends Vps_Component_Generator_GetComponentByClassWithComponentId_Table
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => array(
                'table3' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table3',
                'empty'  => 'Vpc_Basic_Empty_Component'
            ),
            'model' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table2Model'
        );
        return $ret;
    }
}
