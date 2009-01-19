<?php
class Vps_Component_Generator_RecursiveTable_Child extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_RecursiveTable_PageGenerator',
            'component' => 'Vps_Component_Generator_RecursiveTable_Page',
            'nameColumn' => 'name',
            'filenameColumn' => 'name',
            'uniqueFilename' => true,
            'dbIdShortcut' => 'page_',
            'model' => new Vps_Model_FnF(array('data'=>array(
                array('id'=>'foo', 'name' => 'bar')
            )))
        );
        return $ret;
    }

}
