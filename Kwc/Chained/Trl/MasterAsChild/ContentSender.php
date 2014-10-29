<?php
class Kwc_Chained_Trl_MasterAsChild_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    //wofür wird das benötigt?
    //habs *nicht* in Kwc_Chained_Abstract_MasterAsChild_Component gegeben da es da bei ingenieurbueros suche probleme verursacht hat
    //und zwar ist die page nicht die für die sendContent() aufgerufen wird sondern die child, und da fehlen dann die boxen und alles
    public function sendContent($includeMaster)
    {
        $data = $this->_data->getChildComponent('-child');
        $contentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        $contentSender->sendContent($includeMaster);
    }

    public function getLinkDataAttributes()
    {
        $data = $this->_data->getChildComponent('-child');
        $contentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        return $contentSender->getLinkDataAttributes();
    }

}
