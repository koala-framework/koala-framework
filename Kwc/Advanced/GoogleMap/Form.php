<?php
class Kwc_Advanced_GoogleMap_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->setLabelWidth(120);
        $this->fields->add(new Kwf_Form_Field_GoogleMapsField('coordinates', trlKwf('Coordinates')));
        $this->fields->add(new Kwf_Form_Field_Select('zoom', trlKwf('Zoomlevel')))
            ->setAllowBlank(false)
            ->setValues($this->_getZoomLevels())
            ->setWidth(120);

        $this->fields->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setAllowBlank(true)
            ->setWidth(120)
            ->setComment(trlKwfStatic('optional, if empty full width'));

        $this->fields->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setAllowBlank(false)
            ->setWidth(120);

        $this->fields->add(new Kwf_Form_Field_Checkbox('zoom_control', trlKwf('Zoom Control')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('map_type_control', trlKwf('MapType Control')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('routing', trlKwf('Routing')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('scrollwheel', trlKwf('Enable Scrollwheel to zoom')));

        $form = Kwc_Abstract_Form::createChildComponentForm($class, '-text');
        $form->fields->getByName('content')->setHeight(170);
        $this->fields->add($form);
    }

    protected function _getZoomLevels()
    {
        $zoomLevels = array();
        for ($i = 0; $i <= 20; $i++) {
            if ($i == 0) {
                $zommText = ' ('.trlKwf('Worldview').')';
            } else if ($i == 20) {
                $zommText = ' ('.trlKwf('Detailview').')';
            } else {
                $zommText = '';
            }
            $zoomLevels[$i] = $i.$zommText;
        }
        return $zoomLevels;
    }
}
