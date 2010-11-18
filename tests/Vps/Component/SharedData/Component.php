<?php
class Vps_Component_SharedData_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vps_Component_SharedData_Detail_Component',
            'model' => new Vps_Model_FnF(array(
                'data' => array(
                    array('id' => 1, 'name' => 'detail1'),
                    array('id' => 2, 'name' => 'detail2')
                )
            )),
            'filenameColumn' => 'name',
            'nameColumn' => 'name'
        );

        return $ret;
    }
}
?>