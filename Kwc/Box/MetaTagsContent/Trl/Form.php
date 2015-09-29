<?php
class Kwc_Box_MetaTagsContent_Trl_Form extends Kwc_Abstract_Composite_Trl_Form
{
    protected $_createFieldsets = true;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_ShowField('original_description', trlKwf('Original {0}', 'META Description')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('description'));
        $this->add(new Kwf_Form_Field_TextArea('description', 'META Description')) //no trl
            ->setWidth(400)
            ->setHeight(50)
            ->setHelpText(trlKwf('Optional, but important for SEO. Short description of the page content in about 170 characters. Important for Google, Facebook, etc.'));


        $this->add(new Kwf_Form_Field_ShowField('original_og_title', trlKwf('Original {0}', 'Open Graph Title')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('og_title'));
        $this->add(new Kwf_Form_Field_TextField('og_title', 'Open Graph Title'))
            ->setWidth(400);

        $this->add(new Kwf_Form_Field_ShowField('original_og_description', trlKwf('Original {0}', 'Open Graph Description')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('og_description'));
        $this->add(new Kwf_Form_Field_TextArea('og_description', 'Open Graph Description'))
            ->setWidth(400)
            ->setHeight(50);

        parent::_initFields();
    }
}
