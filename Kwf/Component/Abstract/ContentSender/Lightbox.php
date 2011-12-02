<?php
class Kwf_Component_Abstract_ContentSender_Lightbox extends Kwf_Component_Abstract_ContentSender_Default
{
    protected function _getOptions()
    {
        $ret = array();
        if (Kwc_Abstract::hasSetting($this->_data->componentClass, 'lightboxOptions')) {
            $ret =  Kwc_Abstract::getSetting($this->_data->componentClass, 'lightboxOptions');
        }
        $ret['width'] = $this->_data->getComponent()->getContentWidth();
        return $ret;
    }

    public function sendContent($includeMaster = true)
    {
        header('Content-Type: text/html; charset=utf-8');
        $parent = $this->_data->parent->getPage();

        //processInput parent *and* ourself
        if ($includeMaster) {
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
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
            $class = 'kwfLightbox';
            if (isset($options['style'])) $class .= " kwfLightbox$options[style]";
            $options = htmlspecialchars(json_encode($options));
            $lightboxContent = "<div class=\"$class\">\n".
                "<div class=\"kwfLightboxInner\" style=\"$style\">\n".
                "<input type=\"hidden\" class=\"options\" value=\"$options\" />".
                "$lightboxContent\n</div>\n</div>\n";
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
