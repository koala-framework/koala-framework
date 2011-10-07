<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table2',
            'model' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_TableModel'
        );
        return $ret;
    }

}
