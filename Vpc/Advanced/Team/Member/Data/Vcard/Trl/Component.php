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
        if (Vpc_Abstract::hasSetting($teamComponent->componentClass, 'defaultVcardValues')) {
            $setting = Vpc_Abstract::getSetting($teamComponent->componentClass, 'defaultVcardValues');
        }

        if (isset($setting)) {
            return $setting;
        } else {
            return Vpc_Abstract::getSetting($this->getData()->chained->componentClass, 'defaultVcardValues');
        }
    }
}
