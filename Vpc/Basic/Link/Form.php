<?php
class Vpc_Basic_Link_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Link_Index $component)
    {
        parent::__construct($component);

        $tabs = new Vps_Auto_Container_Tabs();
        $tabs->setActiveTab(0);

        $intern = $tabs->add('Intern');
        $intern->setEnabled($component->getSetting('type') != 'intern');
        $intern->add(new Vps_Auto_Field_Vpc_Link('target', 'Target'));
        $intern->add(new Vps_Auto_Field_TextField('rel', 'rel'));

        $extern = $tabs->add('Extern');
        $extern->setEnabled($component->getSetting('type') != 'extern')
            ->setWidth(300);
        $extern->add(new Vps_Auto_Field_TextField('target', 'Target'));

        $this->add($tabs);
    }
}