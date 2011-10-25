<?php
class Kwc_Basic_ImageEnlarge_Trl_Form extends Kwc_Abstract_Image_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-linkTag");
        if (count($form->fields)) {
            $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Click on Preview Image').':'));
            $fs->add($form);
        }
    }
}