<?php
class Kwc_List_Switch_ItemPage_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function sendContent($includeMaster = true)
    {
        $largeContent = $this->_data->parent
            ->getChildComponent('-'.$this->_data->id)
            ->getChildComponent('-large');

        if ($includeMaster) {
            $plugin = Kwf_Component_Plugin_View_Abstract::getInstance('Kwc_List_Switch_LargeContentPlugin', $this->_data->parent->componentId);

            $plugin->setCurrentItem($largeContent);
            $this->_data = $this->_data->getParentPage(); //render parent
            parent::sendContent($includeMaster);
        } else {
            $this->_data = $largeContent;
            parent::sendContent($includeMaster);
        }
    }
}
