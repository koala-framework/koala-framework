<?php
class Vpc_Basic_LinkTag_Extern_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_UrlField('target', trlVps('Url')))
            ->setWidth(450)
            ->setHelpText(hlpVps('vpc_basic_linktag_extern_target'))
            ->setAllowBlank(false);

        if (Vpc_Abstract::getSetting($class, 'hasPopup')) {

            // cards container erstellen und zu form hinzufügen
            $cards = $this->add(new Vps_Form_Container_Cards('open_type', trlVps('Open in')));
            $cards->getCombobox()->setAllowBlank(false);
            $card = $cards->add();
                $card->setTitle(trlVps('Own window'));
                $card->setName('self');
            $card = $cards->add();
                $card->setTitle(trlVps('New window'));
                $card->setName('blank');
            $card = $cards->add();
                $card->setTitle(trlVps('Popup'));
                $card->setName('popup');

                $card->add(new Vps_Form_Field_TextField('width', 'Width'))
                    ->setValue(400)
                    ->setAllowBlank(false)
                    ->setVtype('alphanum');
                $card->add(new Vps_Form_Field_TextField('height', 'Height'))
                    ->setValue(400)
                    ->setAllowBlank(false)
                    ->setVtype('alphanum');
                $card->add(new Vps_Form_Field_Checkbox('menubar', 'Menubar'));
                $card->add(new Vps_Form_Field_Checkbox('toolbar', 'Toolbar'));
                $card->add(new Vps_Form_Field_Checkbox('locationbar', 'Locationbar'));
                $card->add(new Vps_Form_Field_Checkbox('statusbar', 'Statusbar'));
                $card->add(new Vps_Form_Field_Checkbox('scrollbars', 'Scrollbars'));
                $card->add(new Vps_Form_Field_Checkbox('resizable', 'Resizable'));
        }
    }
}
