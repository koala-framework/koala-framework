<?php
class Vps_Component_Generator_ChildPage_Child extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'nameColumn' => 'id',
            'model' => new Vps_Model_FnF(array(
                'columns' => array('id', 'component_id', 'filename'),
                'data'=>array(
                    array('id'=>1, 'component_id'=>'root-child', 'filename' => 'foo'),
                    array('id'=>2, 'component_id'=>'root-child', 'filename' => 'bar'),
                    array('id'=>3, 'component_id'=>'root-child', 'filename' => 'foobar')
                )))
        );
        return $ret;
    }
}
?>