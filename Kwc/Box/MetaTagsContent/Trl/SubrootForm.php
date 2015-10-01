<?php
class Kwc_Box_MetaTagsContent_Trl_SubrootForm extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_ShowField('original_og_title', trlKwf('Original {0}', 'Open Graph Title')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('og_title'));
        $this->add(new Kwf_Form_Field_TextField('og_title', 'Open Graph Title'))
            ->setWidth(350)
            ->setComment(trlKwf('for child pages'));

        $this->add(new Kwf_Form_Field_ShowField('original_og_og_site_name', trlKwf('Original {0}', 'Open Graph Site Name')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('og_og_site_name'));
        $this->add(new Kwf_Form_Field_TextField('og_site_name', 'Open Graph Site Name'))
            ->setWidth(350)
            ->setComment(trlKwf('for child pages'));

        //don't call parent, image is not needed
    }
}
