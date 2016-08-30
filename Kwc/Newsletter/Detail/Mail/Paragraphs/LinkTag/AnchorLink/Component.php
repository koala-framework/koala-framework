<?php
//can be used for anchor links within the mail
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_AnchorLink_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Anchor Link');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['dataClass'] = 'Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_AnchorLink_Data';
        return $ret;
    }
}
