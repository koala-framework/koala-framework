<?php
class Kwc_Basic_Html_Trl_Form extends Kwc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf("Content")));

        $fs->add(new Kwf_Form_Field_TextArea('content', trlKwf('Content')))
            ->setHeight(225)
            ->setWidth(450)
            ->setHideLabel(true)
            ->setAllowTags(true);


        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf("Original")));

        $fs->add(new Kwf_Form_Field_Panel('copy'))
            ->setHideLabel(true)
            ->setXtype('kwc.basic.html.trl.copybutton');

        $fs->fields->add(new Kwf_Form_Field_ShowField('original_content', trlKwf('Original')))
            ->setTpl('{value:htmlEncode}')
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->setHideLabel(true)
            ->getData()->setFieldname('content');
    }
}
