<?php
class Kwc_Misc_UrlInclude_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('url', trlKwf('Url')))
            ->setWidth(300);
    }
}
