<?php
class Kwc_Box_MetaTagsContent_SubrootForm extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('og_title', 'Open Graph Title'))
            ->setWidth(350)
            ->setComment(trlKwf('for child pages'));

        $this->add(new Kwf_Form_Field_TextField('og_site_name', 'Open Graph Site Name'))
            ->setWidth(350)
            ->setComment(trlKwf('for child pages'));

        //don't call parent, image is not needed
    }
}
