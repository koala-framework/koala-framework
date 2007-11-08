<?php
class Vpc_Basic_Link_Extern_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $pageId = null, $componentKey = null)
    {
        parent::__construct($class, $pageId, $componentKey);

        if (Vpc_Abstract::getSetting($class, 'hasLinktext')) {
            $this->add(new Vps_Auto_Field_TextField('text', 'Linktext'))
                ->setWidth(500)
                ->setAllowBlank(false);
        }
        $this->add(new Vps_Auto_Field_TextField('target', 'Target'))
            ->setWidth(500)
            ->setAllowBlank(false);
        $this->add(new Vps_Auto_Field_TextField('rel', 'Rel'));
        $this->add(new Vps_Auto_Field_TextField('param', 'Parameters'));
        $popup = new Vps_Auto_Container_FieldSet('Popup');
        $popup->add(new Vps_Auto_Field_TextField('width', 'Width'))
            ->setValue(400)
            ->setAllowBlank(false)
            ->setVtype('alphanum');
        $popup->add(new Vps_Auto_Field_TextField('height', 'Height'))
            ->setValue(400)
            ->setAllowBlank(false)
            ->setVtype('alphanum');
        $popup->add(new Vps_Auto_Field_CheckBox('menubar', 'Menubar'));
        $popup->add(new Vps_Auto_Field_CheckBox('toolbar', 'Toolbar'));
        $popup->add(new Vps_Auto_Field_CheckBox('locationbar', 'Locationbar'));
        $popup->add(new Vps_Auto_Field_CheckBox('statusbar', 'Statusbar'));
        $popup->add(new Vps_Auto_Field_CheckBox('scrollbars', 'Scrollbars'));
        $popup->add(new Vps_Auto_Field_CheckBox('resizeable', 'Resizeable'));

        $this->add($popup)
            ->setCheckboxToggle(true)
            ->setCheckboxName('is_popup');
    }
}