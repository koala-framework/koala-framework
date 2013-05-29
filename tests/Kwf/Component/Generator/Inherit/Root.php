<?php
class Kwf_Component_Generator_Inherit_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root')
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Kwc_Basic_None_Component');

        $ret['generators']['box']['component'] = array(
            'box' => 'Kwf_Component_Generator_Inherit_Box'
        );

        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
            'name' => 'Static'
        );
        unset($ret['generators']['title']);
        $ret['editComponents'] = array('box');
        return $ret;
    }
}
