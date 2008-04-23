<?php
class Vpc_Advanced_GoogleMap_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->setLabelWidth(120);
        $this->fields->add(new Vps_Form_Field_GoogleMapsField('coordinates', trlVps('Coordinates')));
        $this->fields->add(new Vps_Form_Field_Select('zoom', trlVps('Zoomlevel')))
            ->setAllowBlank(false)
            ->setValues($this->_getZoomLevels())
            ->setWidth(120);

        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setAllowBlank(false)
            ->setWidth(120);

        $this->fields->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setAllowBlank(false)
            ->setWidth(120);

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $form = new Vpc_Basic_Text_Form('text', $classes['text']);
        $form->setComponentIdTemplate('{0}-text');
        $this->fields->add($form);


        $this->fields->add(new Vps_Form_Field_Select('zoom_properties', 'Zoomeinstellungen'))
            ->setValues(array(array('0', trlVps('Move + Zoom')),
                              array('1', trlVps('Move + Zoom (without zoombar)')),
                              array('2', trlVps('Just Zoom'))))
            ->setWidth(300)
            ->setAllowBlank(false);

        $this->fields->add(new Vps_Form_Field_Checkbox('scale', trlVps('Scale')));
        $this->fields->add(new Vps_Form_Field_Checkbox('satelite', trlVps('Satelitemap')));
        $this->fields->add(new Vps_Form_Field_Checkbox('overview', trlVps('Overviewmap')));

    }

    protected function _getZoomLevels()
    {
        $zoomLevels = array();
        for ($i = 0; $i <= 20; $i++) {
            if ($i == 0) {
                $zommText = trlVps(' (Worldview)');
            } else if ($i == 20) {
                $zommText = trlVps(' (Detailview)');
            } else {
                $zommText = '';
            }
            $zoomLevels[$i] = $i.$zommText;
        }
        return $zoomLevels;
    }
}
