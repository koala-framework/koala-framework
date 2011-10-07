<?php
class Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table2',
            'model' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_TableModel'
        );
        return $ret;
    }

}
