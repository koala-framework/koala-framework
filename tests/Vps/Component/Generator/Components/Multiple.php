<?php
class Vps_Component_Generator_Components_Multiple extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Html_Component'
        );
        $ret['generators']['multiBox'] = array(
            'class' => 'Vps_Component_Generator_MultiBox_Static',
            'component' => 'Vps_Component_Generator_Components_Flag'
        );
        $ret['generators']['pageStatic'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Components_Flag',
            'unique' => true
        );
        $ret['generators']['pageTable'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => array(
                'editComponent' => 'Vpc_Basic_Empty_Component',
                'flag' => 'Vps_Component_Generator_Components_Flag'
            ),
            'nameColumn' => 'name',
            'model' => new Vps_Model_FnF(
                array('columns'=>array('id', 'name', 'component'),
                      'data' => array(
                        array('id' => 1, 'name' => 'test1', 'component' => 'editComponent'),
                        array('id' => 2, 'name' => 'test2', 'component' => 'flag')
                    )
                )
            ),
        );
        $ret['generators']['pseudoPageTable'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => 'Vps_Model_FnF',
            'inherit' => true
        );
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Image_Component',
            'unique' => true,
            'inherit' => true
        );
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => 'Vps_Model_FnF',
        );
        $ret['plugins'] = array(
            'Vps_Component_Generator_Components_Plugin'
        );
        $ret['editComponents'] = array('pseudoPageTable', 'multiBox', 'pageTable');
        return $ret;
    }
}
?>