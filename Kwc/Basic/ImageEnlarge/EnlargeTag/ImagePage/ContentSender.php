<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_ContentSender extends Kwf_Component_Abstract_ContentSender_Lightbox
{
    protected function _getOptions()
    {
        $options = array();
        if (Kwc_Abstract::hasSetting($this->_data->componentClass, 'lightboxOptions')) {
            $options =  Kwc_Abstract::getSetting($this->_data->componentClass, 'lightboxOptions');
        }
        $dim = $this->_data->parent->getComponent()->getImageDimensions();
        $options['width'] = $dim['width'];
        $options['height'] = $dim['height'];
        if (!isset($options['style'])) $options['style'] = 'CenterBox'; //default style
        $options['adaptHeight'] = true;
        return $options;
    }
}
