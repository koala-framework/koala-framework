<?php
class Vpc_Abstract_Image_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        // Dateiname
        if (Vpc_Abstract::getSetting($this->getClass(), 'editFilename')) {
            $this->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
                ->setVtype('alphanum');
        }

        // Fileupload
        $image = new Vps_Form_Field_File('Image', Vpc_Abstract::getSetting($this->getClass(), 'imageLabel'));
        $image
            ->setAllowBlank(Vpc_Abstract::getSetting($this->getClass(), 'allowBlank'))
            ->setAllowOnlyImages(true);
        if (Vpc_Abstract::getSetting($this->getClass(), 'maxResolution')) {
            $image->setMaxResolution(Vpc_Abstract::getSetting($this->getClass(), 'maxResolution'));
        }
        $this->add($image);

        if (Vpc_Abstract::getSetting($this->getClass(), 'showHelpText')) {
            $dimensions = Vpc_Abstract::getSetting($this->getClass(), 'dimensions');
            $helptext = trlVps('Size of Target Image') . ': ' . $dimensions[0]['width'] . 'x' . $dimensions[0]['height'] . 'px';
            $helptext .= "<br />" . trlVps('If size does not fit, scale method will be') . ': ' . $dimensions[0]['scale'];
            $this->getByName('Image')->setHelpText($helptext);
        }

        // Höhe, Breite
        $dimensions = Vpc_Abstract::getSetting($this->getClass(), 'dimensions');
        if (count($dimensions) > 1) {
            $this->add(new Vpc_Abstract_Image_DimensionField('dimension', trlVps('Dimension')))
                ->setAllowBlank(false)
                ->setDimensions($dimensions);
        }

        // Bildunterschrift
        if (Vpc_Abstract::getSetting($this->getClass(), 'imageCaption')) {
            $this->add(new Vps_Form_Field_TextField('image_caption', trlVps('Image caption')))
                ->setWidth(300);
        }

        parent::_initFields();
        //absichtlich nicht aufrufen: parent::_initFields();
        //benötigen wir hier nicht, und abgeleitete komponenten können es noch tun
    }

    public function setFieldLabel($label)
    {
        if ($label) {
            $this->fields['Image']->setFieldLabel($label);
        }
        return $this;
    }
}
