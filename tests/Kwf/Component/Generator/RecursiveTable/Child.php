<?php
class Kwf_Component_Generator_RecursiveTable_Child extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'component' => 'Kwc_Basic_None_Component',
            'nameColumn' => 'name',
            'filenameColumn' => 'name',
            'uniqueFilename' => true,
            'model' => new Kwf_Model_FnF(array(
                'columns' => array('id', 'name', 'component_id'),
                'data'=>array(
                    array('id'=>1, 'name' => 'bar', 'component_id'=>'root-1')
                )))
        );
        return $ret;
    }

}
