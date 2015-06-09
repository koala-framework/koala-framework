<?php
class Kwc_Guestbook_Write_Trl_Component extends Kwc_Posts_Write_Trl_Component
{
    public function getSettingsRow()
    {
        return $this->getData()->chained->parent->getComponent()->getRow();
    }

}
