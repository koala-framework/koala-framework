<?php
class Kwc_Newsletter_Unsubscribe_Form_Component extends Kwc_Form_Component
{
    public $_recipient; //set by Kwc_Newsletter_Unsubscribe_Component

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_Unsubscribe_Form_Success_Component';
        $ret['placeholder']['submitButton'] = trlKwfStatic('Unsubscribe newsletter');
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Subscribe_Model'));
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }

        $this->getParentField()->add(new Kwf_Form_Field_ShowField('firstname_interface', trlKwfStatic('Firstname')))
            ->setData(new Kwc_Newsletter_Unsubscribe_Form_RecipientData('getMailFirstname'));
        $this->getParentField()->add(new Kwf_Form_Field_ShowField('lastname_interface', trlKwfStatic('Lastname')))
            ->setData(new Kwc_Newsletter_Unsubscribe_Form_RecipientData('getMailLastname'));
        $this->getParentField()->add(new Kwf_Form_Field_ShowField('email_interface', trlKwfStatic('E-Mail')))
            ->setData(new Kwc_Newsletter_Unsubscribe_Form_RecipientData('getMailEmail'));
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
