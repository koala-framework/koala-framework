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
            if ($parent->componentId == $this->_data->componentId) {
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
        $kwfUniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
        if ($kwfUniquePrefix) $kwfUniquePrefix = $kwfUniquePrefix.'-';
        if ($includeMaster) {
            $parent = $this->_getParent();
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $parentContent = $parentContentSender->_render($includeMaster, $hasDynamicParts);
            $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $parentContent, $match) ? $match[1] : null;

            //remove main content to avoid duplicate content for search engines
            //content will be loaded using ajax
            $startPos = strpos($parentContent, '<main class="'.$kwfUniquePrefix.'kwfMainContent">');
            $endPos = strpos($parentContent, '</main><!--/'.$kwfUniquePrefix.'kwfMainContent-->');
            $parentContent = substr($parentContent, 0, $startPos)
                            .'<main class="'.$kwfUniquePrefix.'kwfMainContent" data-kwc-component-id="'.$parent->componentId.'">'
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
            $class = $kwfUniquePrefix.'kwfLightbox';
            $class .= " ".$kwfUniquePrefix."kwfLightbox$options[style]";
            if (isset($options['cssClass'])) {
                $class .= ' ' . str_replace('kwfUp-', $kwfUniquePrefix, $options['cssClass']);
            }
            if (isset($options['adaptHeight']) && $options['adaptHeight']) $class .= " adaptHeight";
            $options = Kwf_Util_HtmlSpecialChars::filter(json_encode($options));
            $lightboxContent =
                "<div class=\"$class ".$kwfUniquePrefix."kwfLightboxOpen\" data-parent-title=\"$title\">\n".
                "    <div class=\"".$kwfUniquePrefix."kwfLightboxScrollOuter\">\n".
                "        <div class=\"".$kwfUniquePrefix."kwfLightboxScroll\">\n".
                "            <div class=\"".$kwfUniquePrefix."kwfLightboxBetween\">\n".
                "               <div class=\"".$kwfUniquePrefix."kwfLightboxBetweenInner\">\n".
                "                   <div class=\"".$kwfUniquePrefix."kwfLightboxInner\" style=\"$style\">\n".
                "                       <input type=\"hidden\" class=\"options\" value=\"$options\" />\n".
                "                       <div class=\"".$kwfUniquePrefix."kwfLightboxContent\">\n".
                "                           $lightboxContent\n".
                "                       </div>\n".
                "                       <a class=\"".$kwfUniquePrefix."closeButton\" href=\"$parent->url\"><span class=\"".$kwfUniquePrefix."innerCloseButton\">". $this->_data->trlKwf('Close') ."</span></a>\n".
                "                   </div>\n".
                "               </div>\n".
                "            </div>\n".
                "        </div>\n".
                "        <div class=\"".$kwfUniquePrefix."kwfLightboxMask ".$kwfUniquePrefix."kwfLightboxMaskOpen\"></div>\n".
                "    </div>\n".
                "</div>\n";
            $ret = preg_replace('#(<body[^>]*>)#', "\\1\n".$lightboxContent, $parentContent);
            if (preg_match('#<html[^>]* class#', $ret)) {
                $ret = preg_replace('#(<html[^>]*?)( class="([^"]*)")#', "\\1 class=\"\\3 ".$kwfUniquePrefix."kwfLightboxActive\"", $ret);
            } else {
                $ret = preg_replace('#(<html[^>]*)#', "\\1 class=\"\\3".$kwfUniquePrefix."kwfLightboxActive\"", $ret);
            }
            return $ret;
        } else {
            return $lightboxContent;
        }
    }

    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        $options = $this->_getOptions();
        if (isset($options['cssClass'])) {
            $kwfUniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
            if ($kwfUniquePrefix) $kwfUniquePrefix = $kwfUniquePrefix . '-';
            $options['cssClass'] = str_replace('kwfUp-', $kwfUniquePrefix, $options['cssClass']);
        }
        $ret['kwc-lightbox'] = json_encode((object)$options);
        return $ret;
    }

    public function getLinkClass()
    {
        return 'kwfUp-kwcLightbox';
    }
}
