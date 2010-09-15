<?php
class Vpc_Newsletter_Unsubscribe_Component extends Vpc_Form_Component
{
    protected $_recipient;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] =
            'Vpc_Newsletter_Unsubscribe_Success_Component';
        $ret['placeholder']['submitButton'] = trlVpsStatic('Unsubscribe newsletter');
        return $ret;
    }

    public function processMailRedirectInput($recipient, $params)
    {
        $this->_recipient = $recipient;
        if (!($recipient instanceof Vpc_Mail_Recipient_UnsubscribableInterface)) {
            throw new Vps_Exception("To unsubscribe from a newsletter, the recipient row must implement 'Vpc_Mail_Recipient_UnsubscribableInterface'");
        }
        $this->processInput($params);
    }

    protected function _initForm()
    {
        parent::_initForm();
        if ($this->_recipient) {
            $this->_form->setModel($this->_recipient->getModel());
            $this->_form->setId($this->_recipient->id);

            $this->getParentField()->add(new Vps_Form_Field_ShowField('firstname_interface', trlVpsStatic('Firstname')))
                ->setData(new Vpc_Newsletter_Unsubscribe_RecipientData('getMailFirstname'));
            $this->getParentField()->add(new Vps_Form_Field_ShowField('lastname_interface', trlVpsStatic('Lastname')))
                ->setData(new Vpc_Newsletter_Unsubscribe_RecipientData('getMailLastname'));
            $this->getParentField()->add(new Vps_Form_Field_ShowField('email_interface', trlVpsStatic('E-Mail')))
                ->setData(new Vpc_Newsletter_Unsubscribe_RecipientData('getMailEmail'));
        }
    }

    // Falls Unterkomponente will, das Felder zB in ein Fieldset hinzugefügt werden
    protected function getParentField()
    {
        return $this->_form;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        // Wird von redirect component eingebunden, obwohl sie direkt unter
        // newsletter liegt. Dadurch dass die action '' ist, bleibt die form
        // nach dem abschicken auf der selben seite
        $ret['action'] = '';
        return $ret;
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $row->mailUnsubscribe();
    }
}
