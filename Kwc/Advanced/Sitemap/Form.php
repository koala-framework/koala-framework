<?php
class Kwc_Advanced_Sitemap_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_PageSelect('target', trlKwf('Target')))
            ->setControllerUrl(Kwc_Admin::getInstance($class)->getControllerUrl('Pages'))
            ->setWidth(233)
            ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_NumberField('levels', trlKwf('Levels')))
            ->setAllowBlank(false)
            ->setMinValue(1)
            ->setAllowDecimals(false)
            ->setMaxValue(5)
            ->setWidth(50);
    }
}