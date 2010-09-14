<?php
class Vpc_Basic_ImageEnlarge_Trl_Form extends Vpc_Abstract_Image_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-linkTag");
        if (count($form->fields)) {
            $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Click on Preview Image').':'));
            $fs->add($form);
        }
    }
}