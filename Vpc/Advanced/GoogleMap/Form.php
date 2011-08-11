<?php
class Vpc_Advanced_GoogleMap_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

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

        $this->fields->add(new Vps_Form_Field_Select('zoom_properties', trlVps('Zoom properties')))
            ->setValues(array(
                '0' => trlVpsStatic('Move + Zoom'),
                '1' => trlVpsStatic('Move + Zoom (without zoombar)'),
                '2' => trlVpsStatic('None')
            ))
            ->setWidth(300)
            ->setAllowBlank(false);

        $this->fields->add(new Vps_Form_Field_Checkbox('scale', trlVps('Scale')));
        $this->fields->add(new Vps_Form_Field_Checkbox('satelite', trlVps('Satelitemap')));
        $this->fields->add(new Vps_Form_Field_Checkbox('overview', trlVps('Overviewmap')));
        $this->fields->add(new Vps_Form_Field_Checkbox('routing', trlVps('Routing')));

        $form = Vpc_Abstract_Form::createChildComponentForm($class, '-text');
        $form->fields->getByName('content')->setHeight(170);
        $this->fields->add($form);
    }

    protected function _getZoomLevels()
    {
        $zoomLevels = array();
        for ($i = 0; $i <= 20; $i++) {
            if ($i == 0) {
                $zommText = ' ('.trlVps('Worldview').')';
            } else if ($i == 20) {
                $zommText = ' ('.trlVps('Detailview').')';
            } else {
                $zommText = '';
            }
            $zoomLevels[$i] = $i.$zommText;
        }
        return $zoomLevels;
    }
}
