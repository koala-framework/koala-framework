<?php
class Vpc_Advanced_Team_Member_Data_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Team member data');
        $ret['ownModel'] = 'Vpc_Advanced_Team_Member_Data_Model';

        $ret['labelSeparator'] = ':';
        $ret['showLabels'] = true;

        $ret['placeholder']['nameLabel'] = trlVpsStatic('Name');
        $ret['placeholder']['positionLabel'] = trlVpsStatic('Position');
        $ret['placeholder']['phoneLabel'] = trlVpsStatic('Phone');
        $ret['placeholder']['mobileLabel'] = trlVpsStatic('Mobile');
        $ret['placeholder']['emailLabel'] = trlVpsStatic('E-Mail');

        $ret['cssClass'] = 'webStandard webListNone';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['labelSeparator'] = $this->_getSetting('labelSeparator');
        $ret['showLabels'] = $this->_getSetting('showLabels');
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
