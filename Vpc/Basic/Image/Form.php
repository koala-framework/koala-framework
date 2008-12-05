<?php
class Vpc_Basic_Image_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

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
        $dimensions = Vpc_Abstract::getSetting($class, 'dimensions');
        if (is_array($dimensions) && empty($dimensions)) {
            $this->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
                ->setAllowBlank(false)
                ->setMinValue(1)
                ->setMaxValue(9999);
            $this->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
                ->setAllowBlank(false)
                ->setMinValue(1)
                ->setMaxValue(9999);
            $this->add(new Vps_Form_Field_Select('scale', trlVps('Scaling')))
                ->setValues(array(
                        Vps_Media_Image::SCALE_BESTFIT => trlVps('Bestfit'),
                        Vps_Media_Image::SCALE_CROP => trlVps('Crop'),
                        Vps_Media_Image::SCALE_DEFORM => trlVps('Deform')
                    ));
        } else if (is_array($dimensions[0])) {
            $this->add(new Vps_Form_Field_ComboBoxSize('dimension', trlVps('Size')))
                ->setSizes($dimensions);
        }

        // Fileupload
        $this->add(new Vps_Form_Field_File('Image', trlVps('Image')))
            ->setAllowBlank(Vpc_Abstract::getSetting($class, 'allowBlank'))
            ->setAllowOnlyImages(true);
        if (Vpc_Abstract::getSetting($class, 'showHelpText')) {
            $helptext = trlVps('Size of Target Image') . ': ' . $dimensions[0] . 'x' . $dimensions[1] . 'px';
            $helptext .= "<br />" . trlVps('If size does not fit, scale method will be') . ': ' . $dimensions[2];
            $this->getByName('Image')->setHelpText($helptext);
        }
    }

    public function setFieldLabel($label)
    {
        $this->fields['Image']->setFieldLabel($label);
        return $this;
    }
}
