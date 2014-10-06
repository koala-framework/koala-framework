<?php
class Kwc_Directories_Item_Detail_Trl_Form extends Kwc_Directories_Item_Detail_Form
{
    protected function _createChildComponentForm($id, $name = null)
    {
        $ret = parent::_createChildComponentForm($id, $name);
        $ret->setIdTemplate('{0}'.$id);
        return $ret;
    }
}
