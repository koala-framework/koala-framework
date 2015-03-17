<?php
class Kwc_Guestbook_Trl_Component extends Kwc_Posts_Directory_Trl_Component
{
    public function getSettingsRow()
    {
        return $this->getData()->chained->getComponent()->getOwnRow();
    }

}
