<?php
class Vps_Component_Generator_IgnoreVisible_Child extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vps_Component_Generator_IgnoreVisible_Page',
            'nameColumn' => 'id',
            'model' => new Vps_Model_FnF(array('data'=>array(
                array('id'=>1, 'component_id'=>'root-child', 'filename' => 'foo', 'visible'=>1),
                array('id'=>2, 'component_id'=>'root-child', 'filename' => 'bar', 'visible'=>0),
                array('id'=>3, 'component_id'=>'root-child', 'filename' => 'foobar', 'visible'=>0)
            ), 'columns'=>array('id', 'component_id', 'filename', 'visible')))
        );
        return $ret;
    }
}
?>