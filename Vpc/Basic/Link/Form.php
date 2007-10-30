<?php
class Vpc_Basic_Link_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Link_Index $component)
    {
        parent::__construct($component);
        $this->add(new Vps_Auto_Field_Select('type', 'Linktype'))
            ->setValues(array(
                'intern' => 'Intern',
                'extern' => 'Extern',
                'mailto' => 'Mail'
            ));
        $this->add(new Vps_Auto_Field_TextField('target', 'Target'))
            ->setWidth(500);
        $this->add(new Vps_Auto_Field_TextField('rel', 'rel'))
            ->setWidth(500);

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