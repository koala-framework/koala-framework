<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_ContentSender extends Kwf_Component_Abstract_ContentSender_Lightbox
{
    protected function _getOptions()
    {
        $dim = $this->_data->parent->getComponent()->getImageDimensions();
        $options = array(
            'width' => $dim['width'],
            'height' => $dim['height'],
            'style' => 'CenterBox'
        );
        return $options;
    }
}
