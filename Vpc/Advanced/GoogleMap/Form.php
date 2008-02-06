<?php
class Vpc_Advanced_GoogleMap_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
        $this->fields->add(new Vps_Auto_Field_GoogleMapsField('coordinates', 'Koordinaten'));
        $this->fields->add(new Vps_Auto_Field_NumberField('zoom', 'Zoomstufe'))
            ->setAllowNegative(false)
            ->setAllowDecimals(false);

        $this->fields->add(new Vps_Auto_Field_NumberField('width', 'Breite'))
            ->setAllowNegative(false)
            ->setAllowDecimals(false);

        $this->fields->add(new Vps_Auto_Field_NumberField('height', 'Höhe'))
            ->setAllowNegative(false)
            ->setAllowDecimals(false);

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $textId = $id . '-text';
        $form = new Vpc_Basic_Text_Form($classes['text'], $textId);
        $this->fields->add($form);


        $this->fields->add(new Vps_Auto_Field_Select('zoom_properties', 'Zoomeinstellungen'))
            ->setValues(array(array('0', 'Verschieben + Zoom'),
                              array('1', 'Verschieben + Zoom (ohne Zoombalken)'),
                              array('2', 'Nur Zoom')))
            ->setWidth(300);

        $this->fields->add(new Vps_Auto_Field_Checkbox('scale', 'Skalierung'));
        $this->fields->add(new Vps_Auto_Field_Checkbox('satelite', 'Satelitenkarte'));
        $this->fields->add(new Vps_Auto_Field_Checkbox('overview', 'Übersichtskarte'));

    }
}
