<?php
class Vpc_Formular_Contact_Component extends Vpc_Formular_Dynamic_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['tablename'] = 'Vpc_Formular_Contact_Model';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Formular/Contact/Panel.js';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
    }
    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        parent::_afterSave($row);;
        //Todo: Mail schicken
        //Daten: $row->toArray());
        //Empfänger: $this->_getRow()->receiver_mail
    }
}
