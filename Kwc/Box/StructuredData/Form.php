<?php
class Kwc_Box_StructuredData_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $validator = new Zend_Validate_Regex(array(
            'pattern' => Kwc_Box_StructuredData_Component::REGEX
        ));
        $validator->setMessage(trlKwf('No valid json'), Zend_Validate_Regex::NOT_MATCH);

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf("Content")));
        $fs->fields->prepend(new Kwf_Form_Field_TextArea('content'))
            ->setFieldLabel(trlKwf('Content'))
            ->addValidator($validator)
            ->setHideLabel(true)
            ->setHeight(600)
            ->setWidth(850)
            ->setAllowTags(true);
    }
}
