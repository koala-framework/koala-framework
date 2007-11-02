<?php
class Vpc_Basic_Link_Mail_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Link_Mail_Component $component)
    {
        parent::__construct($component);

        if ($component->getSetting('hasLinktext')) {
            $this->add(new Vps_Auto_Field_TextField('text', 'Linktext'))
                ->setWidth(500)
                ->setAllowBlank(false);
        }
        $this->add(new Vps_Auto_Field_TextField('target', 'Target'))
            ->setWidth(500)
            ->setAllowBlank(false);
        $this->add(new Vps_Auto_Field_TextField('mailsubject', 'Predefined Subject for Mail'))
            ->setWidth(300);
        $this->add(new Vps_Auto_Field_TextArea('mailtext', 'Predefined Text for Mail'))
            ->setWidth(300)
            ->setHeight(300);
    }
}