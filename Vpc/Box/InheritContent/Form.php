<?php
class Vpc_Box_InheritContent_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->setModel(new Vps_Model_FnF());
        if (!$this->getClass()) return;
        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-child");
        if ($form) {
            $this->add($form);
        }
    }
}
