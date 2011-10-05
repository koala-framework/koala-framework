<?php
class Vps_Component_Abstract_ContentSender_Lightbox extends Vps_Component_Abstract_ContentSender_Default
{
    protected function _getOptions()
    {
        $ret = array();
        if (Vpc_Abstract::hasSetting($this->_data->componentClass, 'lightboxOptions')) {
            $ret =  Vpc_Abstract::getSetting($this->_data->componentClass, 'lightboxOptions');
        }
        return $ret;
    }

    public function sendContent($includeMaster = true)
    {
        header('Content-Type: text/html; charset=utf-8');
        $parent = $this->_data->parent->getPage();

        //processInput parent *and* ourself
        if ($includeMaster) {
            $parentContentSender = Vpc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $parentProcess = $parentContentSender->_callProcessInput();
        }
        $process = $this->_callProcessInput();

        $lightboxContent = $this->_data->render(null, false);
        if ($includeMaster) {
            $parentContent = $parent->render(null, true);

            //append lightbox after <body> in parent
            $options = $this->_getOptions();
            $style = '';
            if (isset($options['width'])) $style .= "width: $options[width]px;";
            if (isset($options['height'])) $style .= "height: $options[height]px";
            $class = 'vpsLightbox';
            if (isset($options['style'])) $class .= " vpsLightbox$options[style]";
            $lightboxContent = "<div id=\"vpsLightbox\" class=\"$class\" style=\"$style\">\n".
                "$lightboxContent\n</div>\n";
            echo preg_replace('#(<body[^>]*>)#', "\\1\n".$lightboxContent, $parentContent);
        } else {
            echo $lightboxContent;
        }

        if ($includeMaster) {
            $parentContentSender->_callPostProcessInput($parentProcess);
        }
        $this->_callPostProcessInput($process);
    }

    public function getLinkRel()
    {
        $ret = 'lightbox';
        if ($options = $this->_getOptions()) {
            $ret .= json_encode($options);
        }
        return $ret;
    }
}
