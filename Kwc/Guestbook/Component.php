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

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Guestbook');
        $ret['componentCategory'] = 'admin';
        $ret['generators']['detail']['component'] = 'Kwc_Guestbook_Detail_Component';
        $ret['generators']['write']['component'] = 'Kwc_Guestbook_Write_Component';
        $ret['generators']['mail'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Guestbook_Mail_Component'
        );
        $ret['generators']['activate'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Guestbook_ActivatePost_Component'
        );
        $ret['generators']['deactivate'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Guestbook_DeactivatePost_Component'
        );
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwc_Guestbook_ExtConfig';
        $ret['extConfigControllerIndex'] = 'Kwc_Guestbook_ExtConfigControllerIndex';
        $ret['menuConfig'] = 'Kwf_Component_Abstract_MenuConfig_SameClass';
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order('create_time', 'DESC');
        return $ret;
    }
}
