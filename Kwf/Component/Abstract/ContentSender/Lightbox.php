<?php
class Kwf_Component_Abstract_ContentSender_Lightbox extends Kwf_Component_Abstract_ContentSender_Default
{
    protected function _getOptions()
    {
        $ret = array();
        if (Kwc_Abstract::hasSetting($this->_data->componentClass, 'lightboxOptions')) {
            $ret =  Kwc_Abstract::getSetting($this->_data->componentClass, 'lightboxOptions');
        }
        if (!isset($ret['style'])) {
            $ret['style'] = 'CenterBox';
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

        if ($parent instanceof Kwc_Basic_LinkTag_FirstChildPage_Data) {
            $parent = $parent->_getFirstChildPage();
            if ($parent == $this->_data) {
                $parent = $parent->parent;
                while ($parent && $parent instanceof Kwc_Basic_LinkTag_FirstChildPage_Data) {
                    $parent = $parent->parent;
                }
            }
        }

        if (!$parent) {
            $parent = $this->_data->getSubroot()->getChildPage(array('home' => true), array());
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

    protected function _render($includeMaster, &$hasDynamicParts)
    {
        $lightboxContent = $this->_data->render(null, false, $hasDynamicParts);
        if ($includeMaster) {
            $parent = $this->_getParent();
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $parentContent = $parentContentSender->_render($includeMaster, $hasDynamicParts);

            //remove main content to avoid duplicate content for search engines
            //content will be loaded using ajax
            $startPos = strpos($parentContent, '<div class="kwfMainContent">');
            $endPos = strpos($parentContent, '</div><!--/kwfMainContent-->');
            $parentContent = substr($parentContent, 0, $startPos)
                            .'<div class="kwfMainContent" data-kwc-component-id="'.$parent->componentId.'">'
                            .substr($parentContent, $endPos);

            foreach ($this->_data->getRecursiveChildComponents(array('flag' => 'hasInjectIntoRenderedHtml')) as $component) {
                $box = $component;
                $isInBox = false;
                while (!$isInBox && $box) {
                    if (isset($box->box)) $isInBox = true;
                    $box = $box->parent;
                }
                if ($isInBox) {
                    $parentContent = $component->getComponent()->injectIntoRenderedHtml($parentContent);
                }
            }

            //append lightbox after <body> in parent
            $options = $this->_getOptions();
            $style = '';
            if (isset($options['width'])) $style .= "width: $options[width]px;";
            if (isset($options['height'])) $style .= "height: $options[height]px";
            $class = 'kwfLightbox';
            $class .= " kwfLightbox$options[style]";
            if (isset($options['cssClass'])) $class .= " $options[cssClass]";
            if (isset($options['adaptHeight']) && $options['adaptHeight']) $class .= " adaptHeight";
            $options = htmlspecialchars(json_encode($options));
            $lightboxContent =
                "<div class=\"$class kwfLightboxOpen\">\n".
                "    <div class=\"kwfLightboxScrollOuter\">\n".
                "        <div class=\"kwfLightboxScroll\">\n".
                "            <div class=\"kwfLightboxBetween\">\n".
                "               <div class=\"kwfLightboxBetweenInner\">\n".
                "                   <div class=\"kwfLightboxInner\" style=\"$style\">\n".
                "                       <input type=\"hidden\" class=\"options\" value=\"$options\" />\n".
                "                       <div class=\"kwfLightboxContent\">\n".
                "                           $lightboxContent\n".
                "                       </div>\n".
                "                       <a class=\"closeButton\" href=\"$parent->url\"><span class=\"innerCloseButton\">". $this->_data->trlKwf('Close') ."</span></a>\n".
                "                   </div>\n".
                "               </div>\n".
                "            </div>\n".
                "        </div>\n".
                "        <div class=\"kwfLightboxMask kwfLightboxMaskOpen\"></div>\n".
                "    </div>\n".
                "</div>\n";
            return preg_replace('#(<body[^>]*>)#', "\\1\n".$lightboxContent, $parentContent);
        } else {
            return $lightboxContent;
        }
    }

    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        $ret['kwc-lightbox'] = json_encode((object)$this->_getOptions());
        return $ret;
    }
}
