<?php
class Kwc_Abstract_Image_Trl_Image_Form extends Kwc_Abstract_Image_Form
{
    protected function _createImageUploadField($imageLabel)
    {
        $ret = parent::_createImageUploadField($imageLabel);
        $ret->setSelectDimensionDisabled(true);
        return $ret;
    }
}
