<?php
class Vpc_ListChildPages_Teaser_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>400, 'pos'=>1, 'visible'=>true, 'name'=>'name400', 'filename' => 'name400',
                  'parent_id'=>'root', 'component'=>'listchild', 'is_home'=>true, 'category' =>'main', 'hide'=>false),

            array('id'=>500, 'pos'=>1, 'visible'=>true, 'name'=>'name500', 'filename' => 'name500',
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>501, 'pos'=>2, 'visible'=>false, 'name'=>'name501', 'filename' => 'name501',
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>502, 'pos'=>3, 'visible'=>true, 'name'=>'name502', 'filename' => 'name502',
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'listchild' => 'Vpc_List_ChildPages_Teaser_Component',
            'empty' => 'Vpc_Basic_Empty_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
