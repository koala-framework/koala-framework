<?php
class Vpc_Advanced_GoogleMap_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);

        $this->fields->add(new Vps_Auto_Field_GoogleMapsField('coordinates', trlVps('Coordinates')));
        $this->fields->add(new Vps_Auto_Field_Select('zoom', trlVps('Zoomlevel')))
            ->setValues($this->_getZoomLevels());

        $this->fields->add(new Vps_Auto_Field_NumberField('width', trlVps('Width')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false);

        $this->fields->add(new Vps_Auto_Field_NumberField('height', trlVps('Height')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false);

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $textId = $id . '-text';
        $form = new Vpc_Basic_Text_Form($classes['text'], $textId);
        $this->fields->add($form);


        $this->fields->add(new Vps_Auto_Field_Select('zoom_properties', 'Zoomeinstellungen'))
            ->setValues(array(array('0', trlVps('Move + Zoom')),
                              array('1', trlVps('Move + Zoom (without zoombar)')),
                              array('2', trlVps('Just Zoom'))))
            ->setWidth(300);

        $this->fields->add(new Vps_Auto_Field_Checkbox('scale', trlVps('Scale')));
        $this->fields->add(new Vps_Auto_Field_Checkbox('satelite', trlVps('Satelitemap')));
        $this->fields->add(new Vps_Auto_Field_Checkbox('overview', trlVps('Overviewmap')));

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
