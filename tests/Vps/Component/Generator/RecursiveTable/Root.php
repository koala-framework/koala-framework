<?php
class Vps_Component_Generator_RecursiveTable_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vps_Component_Generator_RecursiveTable_Child',
            'nameColumn' => 'id',
            'model' => new Vps_Model_FnF(array('data'=>array(
                array('id'=>1, 'component_id'=>'root', 'filename' => 'table'),
            )))
        );
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_RecursiveTable_Static',
        );
        return $ret;
    }
}
?>