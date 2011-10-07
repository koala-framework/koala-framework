<?php
class Vpc_Misc_UrlInclude_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('url', trlVps('Url')))
            ->setWidth(300);
    }
}
