<?php
class Kwf_Component_Generator_Components_Multiple extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Html_Component'
        );
        $ret['generators']['multiBox'] = array(
            'class' => 'Kwf_Component_Generator_MultiBox_Static',
            'component' => 'Kwf_Component_Generator_Components_Flag'
        );
        $ret['generators']['pageStatic'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_Components_Flag',
            'unique' => true
        );
        $ret['generators']['pageTable'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'component' => array(
                'editComponent' => 'Kwc_Basic_None_Component',
                'flag' => 'Kwf_Component_Generator_Components_Flag'
            ),
            'nameColumn' => 'name',
            'model' => new Kwf_Model_FnF(
                array('columns'=>array('id', 'name', 'component'),
                      'data' => array(
                        array('id' => 1, 'name' => 'test1', 'component' => 'editComponent'),
                        array('id' => 2, 'name' => 'test2', 'component' => 'flag')
                    )
                )
            ),
        );
        $ret['generators']['pseudoPageTable'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Table',
            'component' => 'Kwc_Basic_None_Component',
            'model' => 'Kwf_Model_FnF',
            'inherit' => true
        );
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_Image_Component',
            'unique' => true,
            'inherit' => true
        );
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwc_Basic_None_Component',
            'model' => 'Kwf_Model_FnF',
        );
        $ret['plugins'] = array(
            'Kwf_Component_Generator_Components_Plugin'
        );
        $ret['editComponents'] = array('pseudoPageTable', 'multiBox', 'pageTable');
        return $ret;
    }
}
?>