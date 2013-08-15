<?php
class Kwc_TextImage_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $text = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-text");
        $this->add($text);

        // breite des html-editors ermitteln
        foreach ($text->fields as $f) {
            if (is_instance_of($f, 'Kwf_Form_Field_HtmlEditor')) {
                $editorWidth = $f->getWidth();
                break;
            }
        }

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Image')))
                ->setCheckboxToggle(true)
                ->setCheckboxName('image')
                ->setWidth($editorWidth);

        $image = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-image");
        $fs->add($image);

        $fs = $fs->add(new Kwf_Form_Container_FieldSet(trlKwf('Text/Image - Alignment')))
            ->setName('alignment');
        $fs->add(new Kwf_Form_Field_Radio('position', trlKwf('Alignment')))
            ->setValues(array(
                'left' => trlKwf('Left'),
                'right' => trlKwf('Right'),
                'center' => trlKwf('Center')
            ))
            ->setWidth(210);
        $fs->add(new Kwf_Form_Field_Checkbox('flow', trlKwf('Text flows around Image')));
    }
}
