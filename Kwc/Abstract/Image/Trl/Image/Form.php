<?php
class Kwc_Abstract_Image_Trl_Image_Form extends Kwc_Abstract_Image_Form
{
    protected function _getImageUploadField()
    {
        $ret = parent::_getImageUploadField();
        $ret->setSelectDimensionDisabled(true);
        return $ret;
    }
}
