<?php
class Kwc_Basic_ImageEnlargeParent_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->setCreateMissingRow(true);
        $this->setModel(new Kwf_Model_FnF());

        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-linkTag", 'linkTag');
        $this->add($form);
    }
}