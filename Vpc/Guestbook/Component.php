<?php
class Vpc_Guestbook_Component extends Vpc_Posts_Directory_Component
{
    /**
     * Der Post ist erst inaktiv und muss erst freigeschaltet werden
     */
    const INACTIVE_ON_SAVE = 'inactive_on_save';
    /**
     * Der Post ist sofort aktiv und kann später deaktiviert werden
     */
    const ACTIVE_ON_SAVE = 'active_on_save';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Guestbook');
        $ret['generators']['detail']['component'] = 'Vpc_Guestbook_Detail_Component';
        $ret['generators']['write']['component'] = 'Vpc_Guestbook_Write_Component';
        $ret['generators']['child']['component']['mail'] = 'Vpc_Guestbook_Mail_Component';
        $ret['generators']['child']['component']['activate'] = 'Vpc_Guestbook_ActivatePost_Component';
        $ret['generators']['child']['component']['deactivate'] = 'Vpc_Guestbook_DeactivatePost_Component';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['extConfig'] = 'Vpc_Guestbook_ExtConfig';
        $ret['flags']['hasResources'] = true;
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order('id', 'DESC');
        return $ret;
    }
}
