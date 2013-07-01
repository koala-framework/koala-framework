<?php
class Kwc_List_GalleryBasic_TestComponent extends Kwc_List_Gallery_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_List_GalleryBasic_TestModel';
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id'=>'root_page1', 'columns' => 2)
            )
        ));
        $ret['generators']['child']['component'] = 'Kwc_List_GalleryBasic_ImageEnlarge_TestComponent';
        return $ret;
    }
}
