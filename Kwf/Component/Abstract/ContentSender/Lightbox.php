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

        $parent = $this->_data->parent;
        while ($parent && !$parent->isPage) {
            $previous = $parent;
            $parent = $parent->parent;
        }

        //TODO: the proper solution would be to restructure List_Switch so that the page is our parent
        if (is_instance_of($parent->componentClass, 'Kwc_List_Switch_Component') && $previous) {
            $parent = $parent->getChildComponent('_'.$previous->id);
        }

        $process = $this->getProcessInputComponents();

        //processInput parent *and* ourself
        if ($includeMaster) {
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $process = array_merge($process, $parentContentSender->getProcessInputComponents());
        }
        self::_callProcessInput($process);

        $lightboxContent = $this->_data->render(null, false);
        if ($includeMaster) {
            $parentContent = $parentContentSender->_render($includeMaster);

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

        self::_callPostProcessInput($process);
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
