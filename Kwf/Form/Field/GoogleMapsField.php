<?php
/**
 * @package Form
 */
class Kwf_Form_Field_GoogleMapsField extends Kwf_Form_Field_SimpleAbstract
{
    protected $_latitudeFieldname = null;
    protected $_longitudeFieldname = null;

    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('googlemapsfield');
    }

    // if called, coordinates will be saved into two seperate fields (necessary if you want to use Kwc_Directories_List_ViewMap_Component)
    public function setCoordinateFieldnames($latitudeFieldname = 'latitude', $longitudeFieldname = 'longitude')
    {
        $this->_latitudeFieldname = $latitudeFieldname;
        $this->_longitudeFieldname = $longitudeFieldname;
        return $this;
    }

    public function load($row, $postData = array())
    {
        if ($this->_latitudeFieldname && $this->_longitudeFieldname) {
            return array(
                $this->getFieldName() =>
                    $row->{$this->_latitudeFieldname} . ';' .
                    $row->{$this->_longitudeFieldname}
            );
        } else {
            return parent::load($row, $postData);
        }
    }

    public function prepareSave($row, $postData)
    {
        if ($this->_latitudeFieldname && $this->_longitudeFieldname) {
            if ($this->getSave() !== false) {
                $coords = explode(';', $this->_getValueFromPostData($postData));
                if (isset($coords[0]) && isset($coords[1])) {
                    $row->{$this->_latitudeFieldname} = $coords[0];
                    $row->{$this->_longitudeFieldname} = $coords[1];
                }
            }
        } else {
            return parent::prepareSave($row, $postData);
        }

    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Zend_Validate_Regex("#^(()|(-?[0-9]+\.[0-9]*;-?[0-9]+\.[0-9]*))$#"));
    }
}
