<?php
class Kwc_Advanced_Team_Member_Data_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['vcard'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Advanced_Team_Member_Data_Vcard_Component',
            'name' => trlKwfStatic('vCard')
        );

        $ret['componentName'] = trlKwfStatic('Team member data');
        $ret['ownModel'] = 'Kwc_Advanced_Team_Member_Data_Model';

        $ret['labelSeparator'] = ':';
        $ret['showLabels'] = true;

        $ret['placeholder']['nameLabel'] = trlKwfStatic('Name');
        $ret['placeholder']['positionLabel'] = trlKwfStatic('Position');
        $ret['placeholder']['phoneLabel'] = trlKwfStatic('Phone');
        $ret['placeholder']['mobileLabel'] = trlKwfStatic('Mobile');
        $ret['placeholder']['faxLabel'] = trlKwfStatic('Fax');
        $ret['placeholder']['emailLabel'] = trlKwfStatic('E-Mail');
        $ret['placeholder']['vcardLabel'] = trlKwfStatic('vCard');

        $ret['cssClass'] = 'webStandard webListNone';

        $ret['faxPerPerson'] = false;

        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;

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

    public function getSearchContent()
    {
        $r = $this->_getRow();
        $ret = '';
        $ret .= $r->title;
        $ret .= ' '.$r->firstname;
        $ret .= ' '.$r->lastname;
        $ret .= ' '.$r->workingPosition;
        return $ret;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $text = strip_tags($this->getSearchContent());
        $ret['content'] = $text;
        $ret['normalContent'] = $text;
        return $ret;
    }
}
