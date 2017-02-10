<?php
class Kwc_Guestbook_Write_Component extends Kwc_Posts_Write_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] = 'Kwc_Guestbook_Write_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }

    public function getSettingsRow()
    {
        return $this->getData()->parent->getComponent()->getRow();
    }

    public function getInfoMailComponent()
    {
        return $this->getData()->parent->getChildComponent('_mail');
    }
}
