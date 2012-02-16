<?php
class Kwc_Newsletter_Unsubscribe_Component extends Kwc_Form_Component
{
    protected $_recipient;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_Unsubscribe_Success_Component';
        $ret['placeholder']['submitButton'] = trlKwfStatic('Unsubscribe newsletter');
        return $ret;
    }

    public function processMailRedirectInput($recipient, $params)
    {
        $this->_recipient = $recipient;
        if (!($recipient instanceof Kwc_Mail_Recipient_UnsubscribableInterface)) {
            throw new Kwf_Exception("To unsubscribe from a newsletter, the recipient row must implement 'Kwc_Mail_Recipient_UnsubscribableInterface'");
        }
        $this->processInput($params);
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Subscribe_Model'));
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }

        $this->getParentField()->add(new Kwf_Form_Field_ShowField('firstname_interface', trlKwfStatic('Firstname')))
            ->setData(new Kwc_Newsletter_Unsubscribe_RecipientData('getMailFirstname'));
        $this->getParentField()->add(new Kwf_Form_Field_ShowField('lastname_interface', trlKwfStatic('Lastname')))
            ->setData(new Kwc_Newsletter_Unsubscribe_RecipientData('getMailLastname'));
        $this->getParentField()->add(new Kwf_Form_Field_ShowField('email_interface', trlKwfStatic('E-Mail')))
            ->setData(new Kwc_Newsletter_Unsubscribe_RecipientData('getMailEmail'));
    }

    // Falls Unterkomponente will, das Felder zB in ein Fieldset hinzugefügt werden
    protected function getParentField()
    {
        return $this->_form;
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $row->mailUnsubscribe();
    }
}
