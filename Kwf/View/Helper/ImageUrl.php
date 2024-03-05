<?php
class Kwf_View_Helper_ImageUrl extends Kwf_Component_View_Helper_Image
{
    public function imageUrl($image)
    {
        return Kwf_Util_HtmlSpecialChars::filter($this->_getImageUrl($image));
    }
}
