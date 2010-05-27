<?php
class Vpc_Advanced_Team_Member_Data_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['vcard'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Advanced_Team_Member_Data_Vcard_Component',
            'name' => trlVps('vCard')
        );

        $ret['componentName'] = trlVps('Team member data');
        $ret['ownModel'] = 'Vpc_Advanced_Team_Member_Data_Model';

        $ret['labelSeparator'] = ':';
        $ret['showLabels'] = true;

        $ret['placeholder']['nameLabel'] = trlVpsStatic('Name');
        $ret['placeholder']['positionLabel'] = trlVpsStatic('Position');
        $ret['placeholder']['phoneLabel'] = trlVpsStatic('Phone');
        $ret['placeholder']['mobileLabel'] = trlVpsStatic('Mobile');
        $ret['placeholder']['faxLabel'] = trlVpsStatic('Fax');
        $ret['placeholder']['emailLabel'] = trlVpsStatic('E-Mail');
        $ret['placeholder']['vcardLabel'] = trlVpsStatic('vCard');

        $ret['cssClass'] = 'webStandard webListNone';

        $ret['faxPerPerson'] = false;

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['labelSeparator'] = $this->_getSetting('labelSeparator');
        $ret['showLabels'] = $this->_getSetting('showLabels');
        $ret['vcard'] = $this->getData()->getChildComponent('_vcard');

        $ret['workingPosition'] = $ret['row']->working_position;

        return $ret;
    }

    public function hasContent()
    {
        if ($this->_getRow()) {
            return true;
        }
        return false;
    }
}
