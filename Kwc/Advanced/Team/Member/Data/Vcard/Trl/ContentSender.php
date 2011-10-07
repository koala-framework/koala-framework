<?php
class Vpc_Advanced_Team_Member_Data_Vcard_Trl_ContentSender extends Vpc_Advanced_Team_Member_Data_Vcard_ContentSender
{
    public function sendContent()
    {
        $dataRow = $this->_data->chained->parent->getComponent()->getRow()->toArray();
        $dataRow = (object)array_merge($dataRow, $this->_data->parent->getComponent()->getRow()->toArray());
        $imageData = $this->_data->chained->parent->parent->getChildComponent('-image');
        $this->_outputVcard($dataRow, $imageData);
    }

    /**
     * Set default vCard settings here or in Team_Component
     */
    protected function _getDefaultValues()
    {
        $teamComponent = $this->_data->chained->parent->parent->parent;
        if (Vpc_Abstract::hasSetting($teamComponent->componentClass, 'defaultVcardValues')) {
            $setting = Vpc_Abstract::getSetting($teamComponent->componentClass, 'defaultVcardValues');
        }

        if (isset($setting)) {
            return $setting;
        } else {
            return Vpc_Abstract::getSetting($this->_data->chained->componentClass, 'defaultVcardValues');
        }
    }
}
