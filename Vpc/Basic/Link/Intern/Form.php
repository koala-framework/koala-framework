<?php
class Vpc_Basic_Link_Intern_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Link_Intern_Component $component)
    {
        parent::__construct($component);

        if ($component->getSetting('hasLinktext')) {
            $this->add(new Vps_Auto_Field_TextField('text', 'Linktext'))
                ->setWidth(500)
                ->setAllowBlank(false);
        }

        $this->add(new Vps_Auto_Field_TextField('rel', 'Rel'))
            ->setWidth(500);

        $this->add(new Vps_Auto_Field_TextField('param', 'Parameters'))
            ->setWidth(500);

        $this->add(new Vpc_Basic_Link_Intern_Field('target', 'Target'))
            ->setWidth(500)
            ->setControllerUrl(Vpc_Admin::getInstance($component)->getControllerUrl($component, 'Vpc_Basic_Link_Intern_Pages'));

/*
        $tabs = new Vps_Auto_Container_Tabs();
        $tabs->setActiveTab(0);

        $intern = $tabs->add('Linktype');
        $intern->add(new Vps_Auto_Field_Select('type', 'Linktype'))
            ->setValues(array(
                'intern' => 'Intern',
                'extern' => 'Extern',
                'mailto' => 'Mail'
            ));

        $intern = $tabs->add('Intern');
        $intern->setEnabled($component->getSetting('type') != 'intern');
        $intern->add(new Vps_Auto_Field_Vpc_Link('target', 'Target'));
        $intern->add(new Vps_Auto_Field_TextField('rel', 'rel'));

        $extern = $tabs->add('Extern')
            ->setId('tabExtern');
        $extern->setEnabled($component->getSetting('type') != 'extern')
            ->setWidth(300);
        $extern->add(new Vps_Auto_Field_TextField('target', 'Target'));

        $mailto = $tabs->add('Mail');
        $mailto->setEnabled($component->getSetting('type') != 'mailto')
            ->setWidth(300);
        $mailto->add(new Vps_Auto_Field_TextField('target', 'E-Mail-Address'));

        $this->add($tabs);
*/
    }
}