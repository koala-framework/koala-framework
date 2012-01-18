<?php
class Kwf_Component_Generator_GetComponentByClassWithComponentId_Table extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwc_Basic_None_Component',
            'model' => 'Kwf_Component_Generator_GetComponentByClassWithComponentId_TableModel'
        );
        return $ret;
    }

}
