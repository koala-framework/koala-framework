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

        $this->fields->add(new Kwf_Form_Field_Select('zoom_properties', trlKwf('Zoom properties')))
            ->setValues(array(
                '0' => trlKwfStatic('Move + Zoom'),
                '1' => trlKwfStatic('Move + Zoom (without zoombar)'),
                '2' => trlKwfStatic('None')
            ))
            ->setWidth(300)
            ->setAllowBlank(false);

        $this->fields->add(new Kwf_Form_Field_Checkbox('scale', trlKwf('Scale')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('satelite', trlKwf('Satelitemap')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('overview', trlKwf('Overviewmap')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('routing', trlKwf('Routing')));

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
