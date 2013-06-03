<?php
class Kwc_Trl_SwitchLanguage_Category_Trl_PagesTrlTestModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected $_data = array(
            array('component_id'=>'root-en-main_1', 'visible'=>true, 'name'=>'Home en', 'filename' => 'home_en'),
            array('component_id'=>'root-en-main_2', 'visible'=>false, 'name'=>'Test', 'filename' => 'test_en'),
            array('component_id'=>'root-en-main_3', 'visible'=>true, 'name'=>'Test2 en', 'filename' => 'test2_en'),
            array('component_id'=>'root-en-main_4', 'visible'=>true, 'name'=>'Test3 en', 'filename' => 'test3_en'),
    );
}
