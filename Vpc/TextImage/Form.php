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

        $fs = $fs->add(new Vps_Form_Container_FieldSet(trlVps('Text/Image - Alignment')));
        $fs->add(new Vps_Form_Field_Radio('position', trlVps('Alignment')))
            ->setValues(array(
                'left' => trlVps('Left'),
                'right' => trlVps('Right'),
                'alternate' => trlVps('Alternate'),
            ));
        $fs->add(new Vps_Form_Field_Checkbox('flow', trlVps('Text flows around Image')));
    }
}
