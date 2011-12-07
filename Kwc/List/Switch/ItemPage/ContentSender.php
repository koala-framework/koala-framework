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
            $ret = array_merge($ret, $parentContentSender->getProcessInputComponents());
        }

        return $ret;
    }

    protected function _render($includeMaster)
    {
        $largeContent = $this->_data->parent
            ->getChildComponent('-'.$this->_data->id)
            ->getChildComponent('-large');

        if ($includeMaster) {
            $plugin = Kwf_Component_Plugin_View_Abstract
                ::getInstance('Kwc_List_Switch_LargeContentPlugin', $this->_data->parent->componentId);
            $plugin->setCurrentItem($largeContent);

            //render parent, will include largeContent
            $data = $this->_data->getParentPage();
        } else {
            //render large
            $data = $largeContent;
        }

        $parentContentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
        $parentContentSender = new $parentContentSender($data);
        return $parentContentSender->_render($includeMaster);
    }
}
