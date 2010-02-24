<?php
class Vps_Component_Generator_RecursiveTable2_Table extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => array(
                'empty'=>'Vpc_Basic_Empty_Component',
                'flagged'=>'Vps_Component_Generator_RecursiveTable2_Flagged',
                'table'=>'Vps_Component_Generator_RecursiveTable2_Table',
            ),
            'nameColumn' => 'id',
            'model' => 'Vps_Component_Generator_RecursiveTable2_Model',
        );
        return $ret;
    }

}
