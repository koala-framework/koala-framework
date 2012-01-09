<?php
class Kwf_Component_Generator_GetChildIds_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwc_Basic_None_Component',
            'model' => new Kwf_Model_FnF(array('data'=>array(
                array('id'=>1, 'component_id'=>'root'),
                array('id'=>2, 'component_id'=>'root'),
            )))
        );
        return $ret;
    }
}
