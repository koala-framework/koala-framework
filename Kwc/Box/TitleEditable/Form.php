<?php
class Kwc_Box_TitleEditable_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('title', 'HTML Title')) //no trl
            ->setWidth(400)
            ->setHelpText(trlKwf('Optional, but important for SEO! Meaningful title for this page, max. 70 characters. Appears in the title or tab bar of the browser.'));
    }
}
    
