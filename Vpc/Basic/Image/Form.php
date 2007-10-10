<?php
class Vpc_Basic_Image_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Image_Index $component)
    {
        parent::__construct($component);

        $this->add(new Vps_Auto_Field_TextField('filename', 'Filename'))
            ->setAllowBlank(false);

        //Einstellungen für die Veränderbarkeit der Höhe und Breite
        $sizes = $component->getSetting('size');
        if (empty($sizes)) {
            $this->add(new Vps_Auto_Field_TextField('width', 'Width'));
            $this->add(new Vps_Auto_Field_TextField('height','Height'));
        } else if (is_array($sizes[0])) {
            $this->add(new Vps_Auto_Field_ComboBoxSize('size', 'Size'))
                ->setSizes($sizes);
        }

        if (is_array($component->getSetting('allow'))) {
            $data = array ();
            foreach ($component->getSetting('allow') as $val) {
                $data[] = array($val, $val);
            }
            $this->add(new Vps_Auto_Field_ComboBox('scale', 'Scaling'))
                ->setForceSelection(true)
                ->setStore(array('data' => $data))
                ->setTriggerAction('all')
                ->setEditable(false);
        }

        $this->add(new Vps_Auto_Field_File('vps_upload_id', 'File'))
            ->setDirectory('BasicImage/')
            ->setExtensions($component->getSetting('extensions'));

        $this->add(new Vps_Auto_Field_ImageViewer('vps_upload_id_image', 'Preview'))
            ->setImageUrl($component->getImageUrl())
            ->setPreviewUrl($component->getImageUrl(Vpc_Basic_Image_Index::SIZE_THUMB));
    }
}