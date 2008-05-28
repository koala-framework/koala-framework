<?php
class Vpc_Basic_Image_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        // Kommentar
        if (Vpc_Abstract::getSetting($class, 'editComment')) {
            $this->add(new Vps_Form_Field_TextField('comment', trlVps('Comment')))
                ->setWidth(250);
        }

        // Dateiname
        if (Vpc_Abstract::getSetting($class, 'editFilename')) {
            $this->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
                ->setVtype('alphanum');
        }

        // Höhe, Breite
        $widthField = new Vps_Form_Field_NumberField('width', trlVps('Width'));
        $widthField->setAllowBlank(false);
        $widthField->setMinValue(1);
        $widthField->setMaxValue(9999);
        $heightField = new Vps_Form_Field_NumberField('height', trlVps('Height'));
        $heightField->setAllowBlank(false);
        $heightField->setMinValue(1);
        $heightField->setMaxValue(9999);
        $dimensions = Vpc_Abstract::getSetting($class, 'dimension');
        if (is_array($dimensions) && empty($dimensions)) {
            $this->add($widthField);
            $this->add($heightField);

        } else if (is_array($dimensions[0])) {
            $this->add(new Vps_Form_Field_ComboBoxSize('dimension', trlVps('Size')))
                ->setSizes($dimensions);
        }

        // Skalierungstyp
        $allow = Vpc_Abstract::getSetting($class, 'allow');
        if (is_array($allow) && sizeof($allow) > 1) {
            $this->add(new Vps_Form_Field_Select('scale', trlVps('Scaling')))
                ->setValues($allow);
        }

        // Fileupload
        $this->add(new Vps_Form_Field_File('vps_upload_id', trlVps('File')))
            ->setExtensions(Vpc_Abstract::getSetting($class, 'extensions'))
            ->setAllowBlank(Vpc_Abstract::getSetting($class, 'allowBlank'))
            ->setAllowOnlyImages(true);
    }
}
