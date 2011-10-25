<?php
class Kwf_Component_View_Helper_ImageParam extends Kwf_Component_View_Helper_Image
{
    public function imageParam($image, $param)
    {
        if ($param == 'url') {
            return $this->_getImageUrl($image);
        } else if ($param == 'width' || $param == 'height') {
            $size = $this->_getImageSize($image);
            return $size[$param];
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }
}
