<?php
class Kwc_Basic_Svg_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('File Upload')));
        $fs->add(new Kwf_Form_Field_File('Upload', trlKwf('Svg file')))
            ->setAllowBlank(false)
            ->setAllowOnlySvg(true);
        $fs->add(new Kwf_Form_Field_ShowField('filename', trlKwf('Filename')));
    }
}
