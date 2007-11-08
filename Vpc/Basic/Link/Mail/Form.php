<?php
class Vpc_Basic_Link_Mail_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $pageId = null, $componentKey = null)
    {
        parent::__construct($class, $pageId, $componentKey);

        if (Vpc_Abstract::getSetting($class, 'hasLinktext')) {
            $this->add(new Vps_Auto_Field_TextField('text', 'Linktext'))
                ->setWidth(500)
                ->setAllowBlank(false);
        }
        $this->add(new Vps_Auto_Field_TextField('target', 'E-Mail Address'))
            ->setWidth(500)
            ->setAllowBlank(false);
        $this->add(new Vps_Auto_Field_TextField('mailsubject', 'Predefined Subject for Mail'))
            ->setWidth(300);
        $this->add(new Vps_Auto_Field_TextArea('mailtext', 'Predefined Text for Mail'))
            ->setWidth(300)
            ->setHeight(300);
    }
}