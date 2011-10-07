<?php
class Vps_Component_Generator_GetComponentByClassWithComponentId_Table extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => 'Vps_Component_Generator_GetComponentByClassWithComponentId_TableModel'
        );
        return $ret;
    }

}
