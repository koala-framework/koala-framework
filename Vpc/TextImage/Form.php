<?php
class Vpc_TextImage_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $text = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-text");
        $this->add($text);

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Image')))
                ->setCheckboxToggle(true)
                ->setCheckboxName('image');

        $image = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-image");
        $fs->add($image);

        $fs = $fs->add(new Vps_Form_Container_FieldSet(trlVps('Layout')));
        $fs->add(new Vps_Form_Field_Radio('position', trlVps('Position Image')))
            ->setValues(array(
                'alternate' => trlVps('Alternate'),
                'left' => trlVps('Left'),
                'right' => trlVps('Right')
            ));
        $fs->add(new Vps_Form_Field_Checkbox('flow', trlVps('Text flows around Image')));
    }
}
