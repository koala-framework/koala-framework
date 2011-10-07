<?php
class Kwc_Guestbook_Component extends Kwc_Posts_Directory_Component
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
        $ret['componentName'] = trlKwf('Guestbook');
        $ret['generators']['detail']['component'] = 'Kwc_Guestbook_Detail_Component';
        $ret['generators']['write']['component'] = 'Kwc_Guestbook_Write_Component';
        $ret['generators']['child']['component']['mail'] = 'Kwc_Guestbook_Mail_Component';
        $ret['generators']['child']['component']['activate'] = 'Kwc_Guestbook_ActivatePost_Component';
        $ret['generators']['child']['component']['deactivate'] = 'Kwc_Guestbook_DeactivatePost_Component';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwc_Guestbook_ExtConfig';
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
