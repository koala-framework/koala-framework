<?php
class Kwc_Composite_SwitchDisplay_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->setCreateMissingRow(true);
        $this->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-linktext', 'linktext'));
        $this->add(new Kwf_Form_Field_Checkbox('start_opened', trlKwf('Start opened')));
    }
}
