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

    protected function _render($includeMaster)
    {
        $largeContent = $this->_getLargeContentComponent();

        if ($includeMaster) {
            $plugin = Kwf_Component_Plugin_Abstract
                ::getInstance('Kwc_List_Switch_LargeContentPlugin', $this->_data->parent->componentId);
            $plugin->setCurrentItem($largeContent);
            $plugin->setCurrentPreview($this->_getPreviewComponent());

            //render parent, will include largeContent
            $data = $this->_data->getParentPage();
        } else {
            //render large
            $data = $largeContent;
        }

        if ($data == $this->_data) {
            return parent::_render($includeMaster);
        } else {
            $parentContentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($data);
            return $parentContentSender->_render($includeMaster);
        }
    }

    protected function _getLargeContentComponent()
    {
        return $this->_data->parent
            ->getChildComponent('-'.$this->_data->id)
            ->getChildComponent('-large');
    }

    protected function _getPreviewComponent()
    {
        return $this->_data->parent
            ->getChildComponent('-'.$this->_data->id);
    }
}
