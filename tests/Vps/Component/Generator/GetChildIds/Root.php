<?php
class Vps_Component_Generator_GetChildIds_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vps_Basic_Empty_Component',
            'model' => new Vps_Model_FnF(array('data'=>array(
                array('id'=>1, 'component_id'=>'root'),
                array('id'=>2, 'component_id'=>'root'),
            )))
        );
        return $ret;
    }
}
