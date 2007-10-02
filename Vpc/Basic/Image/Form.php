<?php
class Vpc_Basic_Image_Form extends Vps_Auto_Form
{
    public function __construct($name = null, $id = null, Vpc_Basic_Image_Index $component)
    {
        parent::__construct($name, $id);
        $this->setTable(new Vpc_Basic_Image_IndexModel());
        $this->setFileUpload(true);
        $this->setLoadAfterSave(true);

        $this->add(new Vps_Auto_Field_TextField('name', 'Filename'));

        //Einstellungen für die Veränderbarkeit der Höhe und Breite
        $sizes = $component->getSetting('size');
        if (empty($sizes)) {
            $this->add(new Vps_Auto_Field_TextField('width', 'Width'));
            $this->add(new Vps_Auto_Field_TextField('height','Height'));
        } else {
            $this->add(new Vps_Auto_Field_ComboBoxSize())->setSizes($sizes);
        }

        /*
        if ($component->getSetting('allow') != '' && $component->getSetting('allow') != array()) {
            $styles = $component->getSetting('allow');
            $newStyles = array ();
            foreach ($styles as $data) {
                $newStyles[] = array($data, $data);
            }
            $this->_fields[] = array (
                'type'          => 'ComboBox',
                'fieldLabel'    => 'Settings',
                'name'          => 'style',
                'width'         => 150,
                'store'         => array('data' => $newStyles),
                'hiddenName'    => 'style',
                'editable'      => false,
                'triggerAction' => 'all'
            );
        }
        */

        $this->add(new Vps_Auto_Field_File('vps_upload_id', 'File'))
            ->setDirectory('BasicImage/')
            ->setExtensions($component->getSetting('extensions'));

        $this->add(new Vps_Auto_Field_ImageViewer('vps_upload_id_image', 'Preview'))
            ->setImageUrl($component->getImageUrl())
            ->setPreviewUrl($component->getImageUrl(Vpc_Basic_Image_Index::SIZE_THUMB));
    }
}