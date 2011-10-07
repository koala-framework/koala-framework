<?php
class Kwc_User_Detail_GeneralCommunity_Form extends Kwc_User_Detail_General_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->_generalFieldset->add(new Kwf_Form_Field_TextField('nickname', trlKwf('Nickname')))
                    ->setAllowBlank(false)
                    ->setWidth(250)
                    ->setMaxLength(20)
                    ->addValidator(new Kwf_Validate_Row_Unique());

        $this->_generalFieldset->add(new Kwf_Form_Field_TextField('location', trlKwf('Place of living')))
            ->setWidth(250);

        $this->_generalFieldset->add(new Kwf_Form_Field_TextArea('signature', trlKwf('Signature')))
            ->setWidth(250)
            ->setHeight(100)
            ->setHideInRegister(true);

        $this->_generalFieldset->add(new Kwf_Form_Field_TextArea('description_short', trlKwf('Short Description')))
            ->setWidth(250)
            ->setHeight(100)
            ->setHideInRegister(true);

        if (isset($this->fields['avatar'])) {
            $this->fields['avatar']->setHideInRegister(true);
        }
    }
}
