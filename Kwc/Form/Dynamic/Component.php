<?php
class Kwc_Form_Dynamic_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Form');
        $ret['componentIcon'] = 'application_form';
        $ret['componentCategory'] = 'content';
        $ret['generators']['child']['component']['paragraphs'] = 'Kwc_Form_Dynamic_Paragraphs_Component';
        $ret['generators']['child']['component']['form'] = 'Kwc_Form_Dynamic_Form_Component';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['menuConfig'] = 'Kwc_Form_Dynamic_MenuConfig';
        $ret['extConfig'] = 'Kwc_Form_Dynamic_ExtConfig';
        return $ret;
    }

    public function getMailSettings()
    {
        $spamCheck = true;
        $c = $this->getData();
        while ($c) {
            foreach (Kwc_Abstract::getSetting($c->componentClass, 'plugins') as $plugin) {
                if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                    $spamCheck = false;
                    break;
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }

        $row = $this->getRow();
        return array(
            'recipient' => $row->recipient,
            'recipient_cc' => $row->recipient_cc,
            'subject' => $row->subject,

            'send_confirm_mail' => $row->send_confirm_mail,
            'confirm_field_component_id' => $row->confirm_field_component_id,
            'confirm_subject' => $row->confirm_subject,

            'check_spam' => $spamCheck
        );
    }
}
