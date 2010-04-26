<?php
class Vpc_Advanced_Team_Member_Data_Vcard_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function sendContent()
    {
        $dataRow = $this->getData()->chained->parent->getComponent()->getRow()->toArray();
        $dataRow = (object)array_merge($dataRow, $this->getData()->parent->getComponent()->getRow()->toArray());
        $imageData = $this->getData()->chained->parent->parent->getChildComponent('-image');
        $defaults = $this->_getDefaultValues();
        Vpc_Advanced_Team_Member_Data_Vcard_Component::outputVcard(
            $dataRow, $defaults, $imageData
        );
    }

    /**
     * Set default vCard settings here or in Team_Component
     */
    private function _getDefaultValues()
    {
        $teamComponent = $this->getData()->chained->parent->parent->parent;
        $setting = Vpc_Abstract::getSetting($teamComponent->componentClass, 'defaultVcardValues');

        if ($setting) {
            return $setting;
        } else {
            return $this->_getSetting('defaultVcardValues');
        }
    }
}
