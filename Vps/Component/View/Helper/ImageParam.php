<?php
class Vps_Component_View_Helper_ImageParam extends Vps_Component_View_Helper_Image
{
    public function imageParam($image, $param)
    {
        if ($param == 'url') {
            return $this->_getImageUrl($image);
        } else if ($param == 'width' || $param == 'height') {
            $size = $this->_getImageSize($image);
            return $size[$param];
        } else {
            throw new Vps_Exception_NotYetImplemented();
        }
    }
}
