<?php
class Kwc_Guestbook_Write_Form_Trl_Component extends Kwc_Posts_Write_Form_Trl_Component
{
    public function getSettingsRow()
    {
        return $this->getData()->chained->parent->getComponent()->getSettingsRow();
    }
    public function getInfoMailComponent()
    {
        return $this->getData()->chained->parent->getComponent()->getInfoMailComponent();
    }
}
