<?php
class Vpc_Advanced_Sitemap_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_PageSelect('target', trlVps('Target')))
            ->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl('Pages'))
            ->setWidth(233)
            ->setAllowBlank(false);

        $this->add(new Vps_Form_Field_NumberField('levels', trlVps('Levels')))
            ->setAllowBlank(false)
            ->setMinValue(1)
            ->setAllowDecimals(false)
            ->setMaxValue(5)
            ->setWidth(50);
    }
}