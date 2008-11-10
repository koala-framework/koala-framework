<?php
class Vpc_News_Directory_FormController extends Vpc_Directories_Item_Directory_FormController
{
    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('componentId')) {
            $row->component_id = $this->_getParam('componentId');
            $row->visible = 0;
        }
    }
}
