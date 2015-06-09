<?php
class Kwc_List_Switch_ItemPage_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    protected function _getProcessInputComponents($includeMaster)
    {
        $ret = parent::_getProcessInputComponents($includeMaster);

        //processInput parent *and* ourself
        if ($includeMaster) {
            $parent = $this->_data->getParentPage();
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $ret = array_merge($ret, $parentContentSender->_getProcessInputComponents($includeMaster));
        }

        return $ret;
    }

    protected function _render($includeMaster, &$hasDynamicParts)
    {
        $component = $this->_data->parent->getComponent();
        $largeContent = $component->getLargeComponent($this->_data);

        if ($includeMaster) {
            $plugin = Kwf_Component_Plugin_Abstract::getInstance(
                'Kwc_List_Switch_LargeContentPlugin', $this->_data->parent->componentId
            );
            $plugin->setCurrentItem($largeContent);
            $plugin->setCurrentPreview($component->getPreviewComponent($this->_data));

            //render parent, will include largeContent
            $data = $this->_data->getParentPage();
        } else {
            //render large
            $data = $largeContent;
        }

        if ($data == $this->_data) {
            return parent::_render($includeMaster, $hasDynamicParts);
        } else {
            $parentContentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($data);
            return $parentContentSender->_render($includeMaster, $hasDynamicParts);
        }
    }
}
