<?php
class Kwc_Basic_LinkTag_Extern_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_UrlField('target', trlKwf('Url')))
            ->setWidth(450)
            ->setHelpText(hlpKwf('kwc_basic_linktag_extern_target'))
            ->setAllowBlank(false);

        if (Kwc_Abstract::getSetting($class, 'hasPopup')) {

            // cards container erstellen und zu form hinzufügen
            $cards = $this->add(new Kwf_Form_Container_Cards('open_type', trlKwf('Open in')));
            $cards->getCombobox()->setAllowBlank(false);
            $card = $cards->add();
                $card->setTitle(trlKwf('Same window'));
                $card->setName('self');
            $card = $cards->add();
                $card->setTitle(trlKwf('New window'));
                $card->setName('blank');
            $card = $cards->add();
                $card->setTitle(trlKwf('Popup'));
                $card->setName('popup');

                $card->add(new Kwf_Form_Field_TextField('width', 'Width'))
                    ->setValue(400)
                    ->setAllowBlank(false)
                    ->setVtype('alphanum');
                $card->add(new Kwf_Form_Field_TextField('height', 'Height'))
                    ->setValue(400)
                    ->setAllowBlank(false)
                    ->setVtype('alphanum');
                $card->add(new Kwf_Form_Field_Checkbox('menubar', 'Menubar'));
                $card->add(new Kwf_Form_Field_Checkbox('toolbar', 'Toolbar'));
                $card->add(new Kwf_Form_Field_Checkbox('locationbar', 'Locationbar'));
                $card->add(new Kwf_Form_Field_Checkbox('statusbar', 'Statusbar'));
                $card->add(new Kwf_Form_Field_Checkbox('scrollbars', 'Scrollbars'));
                $card->add(new Kwf_Form_Field_Checkbox('resizable', 'Resizable'));
        }
    }
}
