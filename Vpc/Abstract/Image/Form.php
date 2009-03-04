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
        $this->add(new Vps_Form_Field_File('Image', trlVps('Image')))
            ->setAllowBlank(Vpc_Abstract::getSetting($this->getClass(), 'allowBlank'))
            ->setAllowOnlyImages(true);
        if (Vpc_Abstract::getSetting($this->getClass(), 'showHelpText')) {
            $dimensions = Vpc_Abstract::getSetting($this->getClass(), 'dimensions');
            $helptext = trlVps('Size of Target Image') . ': ' . $dimensions[0]['width'] . 'x' . $dimensions[0]['width'] . 'px';
            $helptext .= "<br />" . trlVps('If size does not fit, scale method will be') . ': ' . $dimensions[0]['scale'];
            $this->getByName('Image')->setHelpText($helptext);
        }

        // Höhe, Breite
        $dimensions = Vpc_Abstract::getSetting($this->getClass(), 'dimensions');
        if (count($dimensions) > 1) {
            $this->add(new Vpc_Abstract_Image_DimensionField('dimension', trlVps('Dimension')))
                ->setDimensions($dimensions);
        }

        //absichtlich nicht aufrufen: parent::_initFields();
        //benötigen wir hier nicht, und abgeleitete komponenten können es noch tun
    }

    public function setFieldLabel($label)
    {
        $this->fields['Image']->setFieldLabel($label);
        return $this;
    }
}
