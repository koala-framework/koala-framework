<?php
class Vpc_Basic_LinkTag_Extern_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_TextField('target', trlVps('URL')))
            ->setWidth(450)
            ->setAllowBlank(false);

        if (Vpc_Abstract::getSetting($class, 'hasPopup')) {
            $popup = new Vps_Form_Container_FieldSet('Popup');
            $popup->add(new Vps_Form_Field_TextField('width', 'Width'))
                ->setValue(400)
                ->setAllowBlank(false)
                ->setVtype('alphanum');
            $popup->add(new Vps_Form_Field_TextField('height', 'Height'))
                ->setValue(400)
                ->setAllowBlank(false)
                ->setVtype('alphanum');
            $popup->add(new Vps_Form_Field_Checkbox('menubar', 'Menubar'));
            $popup->add(new Vps_Form_Field_Checkbox('toolbar', 'Toolbar'));
            $popup->add(new Vps_Form_Field_Checkbox('locationbar', 'Locationbar'));
            $popup->add(new Vps_Form_Field_Checkbox('statusbar', 'Statusbar'));
            $popup->add(new Vps_Form_Field_Checkbox('scrollbars', 'Scrollbars'));
            $popup->add(new Vps_Form_Field_Checkbox('resizable', 'Resizable'));
    
            $this->add($popup)
                ->setCheckboxToggle(true)
                ->setCheckboxName('is_popup');
        }
    }
}
