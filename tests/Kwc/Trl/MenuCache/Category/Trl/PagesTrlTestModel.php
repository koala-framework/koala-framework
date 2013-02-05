<?php
class Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected $_data = array(
            array('component_id'=>'root-en-main_1', 'visible'=>true, 'name'=>'Home en', 'filename' => 'home_en'),
            array('component_id'=>'root-en-main_2', 'visible'=>true, 'name'=>'Test', 'filename' => 'test'),
            array('component_id'=>'root-en-main_3', 'visible'=>true, 'name'=>'Test2 en', 'filename' => 'test2_en'),
            array('component_id'=>'root-en-main_7', 'visible'=>false, 'name'=>'Test7 en', 'filename' => 'test7_en'),
    );
}
