<?php
class Kwc_Box_MetaTagsContent_Form extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextArea('description', 'META Description')) //no trl
            ->setWidth(400)
            ->setHeight(80)
            ->setHelpText(trlKwf('Optional, but important for SEO. Short description of the page content in about 170 characters. Important for Google, Facebook, etc.'));

        $this->add(new Kwf_Form_Field_TextField('og_title', 'Open Graph Title'))
            ->setWidth(400);

        $this->add(new Kwf_Form_Field_TextArea('og_description', 'Open Graph Description'))
            ->setWidth(400)
            ->setHeight(50);

        parent::_initFields();
    }
}
