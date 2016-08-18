<?php
class Kwf_Component_SharedData_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'component' => 'Kwf_Component_SharedData_Detail_Component',
            'model' => new Kwf_Model_FnF(array(
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