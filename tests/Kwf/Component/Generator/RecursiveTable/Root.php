<?php
class Kwf_Component_Generator_RecursiveTable_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Generator_RecursiveTable_Child',
            'nameColumn' => 'id',
            'model' => new Kwf_Model_FnF(array('data'=>array(
                array('id'=>1, 'component_id'=>'root', 'filename' => 'table'),
            )))
        );
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_RecursiveTable_Static',
        );
        return $ret;
    }
}
?>