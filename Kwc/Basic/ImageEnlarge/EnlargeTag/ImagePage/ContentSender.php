<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_ContentSender extends Kwf_Component_Abstract_ContentSender_Lightbox
{
    protected function _getOptions()
    {
        $options = parent::_getOptions();
        $dim = $this->_data->parent->getComponent()->getImageDimensions();
        $options['width'] = $dim['width'];
        $options['height'] = $dim['height'];
        if (!isset($options['style'])) $options['style'] = 'CenterBox'; //default style
        return $options;
    }
}
