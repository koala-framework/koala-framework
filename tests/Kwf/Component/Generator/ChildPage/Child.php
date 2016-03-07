<?php
class Kwf_Component_Generator_ChildPage_Child extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'component' => 'Kwc_Basic_None_Component',
            'nameColumn' => 'filename',
            'model' => new Kwf_Model_FnF(array(
                'columns' => array('id', 'component_id', 'filename'),
                'data'=>array(
                    array('id'=>1, 'component_id'=>'root-child', 'filename' => 'foo'),
                    array('id'=>2, 'component_id'=>'root-child', 'filename' => 'bar'),
                    array('id'=>3, 'component_id'=>'root-child', 'filename' => 'foobar')
                )))
        );
        return $ret;
    }
}
?>
