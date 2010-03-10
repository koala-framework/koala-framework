<?php
class Vpc_Box_InheritContent_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-child", 'child');
        $this->add($form);
    }
}
