<?php
class Vpc_Basic_Flash_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->setLabelWidth(120);

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Flash file')));
        $cards = $fs->add(new Vps_Form_Container_Cards('flash_source_type', trlVps('Flash source')))
            ->setDefaultValue('vps_upload_id_media');

        $card = $cards->add();
        $card->setTitle(trlVps('Upload a Flash file'));
        $card->setName('vps_upload_id_media');
        $card->add(new Vps_Form_Field_File('FileMedia', trlVps('Element')));

        $card = $cards->add();
        $card->setTitle(trlVps('From external source'));
        $card->setName('external_flash_url');
        $card->add(new Vps_Form_Field_TextField('external_flash_url', trlVps('URL to Flash file')))
            ->setWidth(300);

        $fs->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $fs->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $fs->add(new Vps_Form_Field_Checkbox('allow_fullscreen', trlVps('Allow fullscreen')));
        $fs->add(new Vps_Form_Field_Checkbox('menu', trlVps('Menu')));

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Flash variables')));
        $mf = $fs->add(new Vps_Form_Field_MultiFields('FlashVars'));
        $mf->fields->add(new Vps_Form_Field_TextField('key', trlVps('Variable name')))
            ->setLabelWidth(120);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')))
            ->setLabelWidth(120);
    }
}
