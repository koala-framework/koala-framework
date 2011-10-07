<?php
class Vps_Component_Generator_RecursiveTable_Child extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'nameColumn' => 'name',
            'filenameColumn' => 'name',
            'uniqueFilename' => true,
            'model' => new Vps_Model_FnF(array(
                'columns' => array('id', 'name', 'component_id'),
                'data'=>array(
                    array('id'=>1, 'name' => 'bar', 'component_id'=>'root-1')
                )))
        );
        return $ret;
    }

}
