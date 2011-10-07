<?php
class Kwc_Box_InheritContent_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-child", 'child');
        $this->add($form);
    }
}
