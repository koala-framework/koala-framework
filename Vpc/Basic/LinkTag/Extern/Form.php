<?php
class Vpc_Basic_LinkTag_Extern_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);

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
        $popup->add(new Vps_Auto_Field_Checkbox('menubar', 'Menubar'));
        $popup->add(new Vps_Auto_Field_Checkbox('toolbar', 'Toolbar'));
        $popup->add(new Vps_Auto_Field_Checkbox('locationbar', 'Locationbar'));
        $popup->add(new Vps_Auto_Field_Checkbox('statusbar', 'Statusbar'));
        $popup->add(new Vps_Auto_Field_Checkbox('scrollbars', 'Scrollbars'));
        $popup->add(new Vps_Auto_Field_Checkbox('resizeable', 'Resizeable'));

        $this->add($popup)
            ->setCheckboxToggle(true)
            ->setCheckboxName('is_popup');
    }
}
