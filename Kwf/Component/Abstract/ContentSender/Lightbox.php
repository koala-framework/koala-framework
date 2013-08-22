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

    private function _getParent()
    {
        $previous = null;
        $parent = $this->_data->parent;
        while ($parent && (!$parent->isPage || is_instance_of(Kwc_Abstract::getSetting($parent->componentClass, 'contentSender'), 'Kwf_Component_Abstract_ContentSender_Lightbox'))) {
            $previous = $parent;
            $parent = $parent->parent;
        }

        if (!$parent) {
            $parent = Kwf_Component_Data_Root::getInstance()->getChildPage(array('home' => true), array());
        }

        if ($parent instanceof Kwc_Basic_LinkTag_FirstChildPage_Data) {
            $parent = $parent->_getFirstChildPage();
        }

        //TODO: the proper solution would be to restructure List_Switch so that the page is our parent
        if (is_instance_of($parent->componentClass, 'Kwc_List_Switch_Component') && $previous) {
            $parent = $parent->getChildComponent('_'.$previous->id);
        }
        return $parent;
    }

    protected function _getProcessInputComponents($includeMaster)
    {
        $ret = parent::_getProcessInputComponents($includeMaster);

        //processInput parent *and* ourself
        if ($includeMaster) {
            $parent = $this->_getParent();
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $ret = array_merge($ret, $parentContentSender->_getProcessInputComponents($includeMaster));
        }

        return $ret;
    }

    protected function _render($includeMaster)
    {
        $lightboxContent = $this->_data->render(null, false);
        if ($includeMaster) {
            $parent = $this->_getParent();
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $parentContent = $parentContentSender->_render($includeMaster);

            //append lightbox after <body> in parent
            $options = $this->_getOptions();
            $style = '';
            if (isset($options['width'])) $style .= "width: $options[width]px;";
            if (isset($options['height'])) $style .= "height: $options[height]px";
            $class = 'kwfLightbox';
            if (isset($options['style'])) $class .= " kwfLightbox$options[style]";
            if (isset($options['cssClass'])) $class .= " $options[cssClass]";
            $options = htmlspecialchars(json_encode($options));
            $lightboxContent = "<div class=\"$class\">\n".
                "<div class=\"kwfLightboxInner\" style=\"$style\">\n".
                "    <input type=\"hidden\" class=\"options\" value=\"$options\" />\n".
                "    <a class=\"closeButton\" href=\"$parent->url\"></a>\n".
                "    <div class=\"kwfLightboxContent\">\n".
                "        $lightboxContent\n".
                "    </div>\n".
                "</div>\n</div>\n";
            return preg_replace('#(<body[^>]*>)#', "\\1\n".$lightboxContent, $parentContent);
        } else {
            return $lightboxContent;
        }
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
