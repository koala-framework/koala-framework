<?php
class Vpc_Basic_Image_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        // Kommentar
        if (Vpc_Abstract::getSetting($class, 'editComment')) {
            $this->add(new Vps_Auto_Field_TextField('comment', trlVps('Comment')))
                ->setWidth(250);
        }

        // Dateiname
        if (Vpc_Abstract::getSetting($class, 'editFilename')) {
            $this->add(new Vps_Auto_Field_TextField('filename', trlVps('Filename')))
                ->setVtype('alphanum');
        }

        // Höhe, Breite
        $widthField = new Vps_Auto_Field_NumberField('width', 'Width');
        $widthField->setMinValue(1);
        $widthField->setMaxValue(9999);
        $heightField = new Vps_Auto_Field_TextField('height', 'Height');
        $heightField->setMinValue(1);
        $heightField->setMaxValue(9999);
        $dimensions = Vpc_Abstract::getSetting($class, 'dimension');
        if (is_array($dimensions) && empty($dimensions)) {
            $this->add($widthField);
            $this->add($heightField);

        } else if (is_array($dimensions[0])) {
            $this->add(new Vps_Auto_Field_ComboBoxSize('dimension', trlVps('Size')))
                ->setSizes($dimensions);
        }

        // Skalierungstyp
        $allow = Vpc_Abstract::getSetting($class, 'allow');
        if (is_array($allow) && sizeof($allow) > 1) {
            $this->add(new Vps_Auto_Field_Select('scale', trlVps('Scaling')))
                ->setValues($allow);
        }

        // Fileupload
        $this->add(new Vps_Auto_Field_File('vps_upload_id', 'File'))
            ->setExtensions(Vpc_Abstract::getSetting($class, 'extensions'))
            ->setAllowBlank(Vpc_Abstract::getSetting($class, 'allowBlank'))
            ->setAllowOnlyImages(true);

        // Bildvorschau
        $this->add(new Vps_Auto_Field_ImageViewer('vps_upload_id_image', trlVps('Preview')))
            ->setClass($class);
    }
}
