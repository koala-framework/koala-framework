<?php
class Kwf_Component_Generator_RecursiveTable2_Table extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => array(
                'empty'=>'Kwc_Basic_None_Component',
                'flagged'=>'Kwf_Component_Generator_RecursiveTable2_Flagged',
                'table'=>'Kwf_Component_Generator_RecursiveTable2_Table',
            ),
            'nameColumn' => 'id',
            'model' => 'Kwf_Component_Generator_RecursiveTable2_Model',
        );
        return $ret;
    }

}
