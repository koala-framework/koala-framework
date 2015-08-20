<?php
class Kwc_Box_MetaTagsContent_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextArea('description', 'META Description')) //no trl
            ->setWidth(400)
            ->setHeight(80)
            ->setHelpText(trlKwf('Optional, but important for SEO. Short description of the page content in about 170 characters. Important for Google, Facebook, etc.'));
    }
}
