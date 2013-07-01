<?php
class Kwc_Form_Dynamic_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Form');
        $ret['componentIcon'] = new Kwf_Asset('application_form');
        $ret['generators']['child']['component']['paragraphs'] = 'Kwc_Form_Dynamic_Paragraphs_Component';
        $ret['generators']['child']['component']['form'] = 'Kwc_Form_Dynamic_Form_Component';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['editComponents'] = array('paragraphs');
        $ret['menuConfig'] = 'Kwc_Form_Dynamic_MenuConfig';
        return $ret;
    }

    public function getMailSettings()
    {
        $row = $this->getRow();
        return array(
            'recipient' => $row->recipient,
            'recipient_cc' => $row->recipient_cc,
            'subject' => $row->subject,
        );
    }
}
