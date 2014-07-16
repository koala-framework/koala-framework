<?php
class Kwc_Newsletter_Unsubscribe_Form_FrontendForm extends Kwf_Form
{
    protected $_model = 'Kwc_Newsletter_Subscribe_Model';

    protected function _init()
    {
        parent::_init();
        $this->_getParentField()->add(new Kwf_Form_Field_ShowField('firstname_interface', trlKwfStatic('Firstname')))
            ->setData(new Kwc_Newsletter_Unsubscribe_Form_RecipientData('getMailFirstname'));
        $this->_getParentField()->add(new Kwf_Form_Field_ShowField('lastname_interface', trlKwfStatic('Lastname')))
            ->setData(new Kwc_Newsletter_Unsubscribe_Form_RecipientData('getMailLastname'));
        $this->_getParentField()->add(new Kwf_Form_Field_ShowField('email_interface', trlKwfStatic('E-Mail')))
            ->setData(new Kwc_Newsletter_Unsubscribe_Form_RecipientData('getMailEmail'));
    }

    // Falls Unterkomponente will, das Felder zB in ein Fieldset hinzugefügt werden
    protected function _getParentField()
    {
        return $this;
    }
}
