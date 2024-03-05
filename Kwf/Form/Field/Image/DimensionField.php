<?php
class Kwf_Form_Field_Image_DimensionField extends Kwf_Form_Field_Abstract
{
    public function __construct($name = null, $fieldLabel = null)
    {
        parent::__construct($name, $fieldLabel);
        $this->setXtype('kwf.form.field.image.dimensionfield');
    }

    protected $_fieldPrefix = '';
    public function setFieldPrefix($prefix)
    {
        $this->_fieldPrefix = $prefix;
    }

    public function load($row, $postData = array())
    {
        //Standardwert so wie in Kwc_Abstract_Image_Component::getImageDimensions
        $dimensions = $this->getDimensions();
        $dimension = $row->{$this->_fieldPrefix.'dimension'};
        if (!isset($dimensions[$dimension])) {
            $dimension = current(array_keys($dimensions));
        }
        $d = $dimensions[$dimension];

        $cropX = $row->{$this->_fieldPrefix.'crop_x'};
        $cropY = $row->{$this->_fieldPrefix.'crop_y'};
        $cropWidth = $row->{$this->_fieldPrefix.'crop_width'};
        $cropHeight = $row->{$this->_fieldPrefix.'crop_height'};

        $cover = false;
        if (isset($d['cover'])) {
            $cover = $d['cover'];
        }

        $cropData = null;
        if ($cropX !== null && $cropY !== null
            && $cropWidth !== null && $cropHeight !== null
        ) {
            $cropData = array(
                'x' => $cropX,
                'y' => $cropY,
                'width' => $cropWidth,
                'height' => $cropHeight
            );
        }
        $value = array(
            'dimension' => $dimension,
            'width' => $row->{$this->_fieldPrefix.'width'},
            'height' => $row->{$this->_fieldPrefix.'height'},
            'cover' => $cover,
            'cropData' => $cropData
        );
        return array($this->getFieldName() => $value);
    }

    public function prepareSave($row, $postData)
    {
        Kwf_Form_Field_Abstract::prepareSave($row, $postData);
        $value = $this->_getValueFromPostData($postData);
        if (is_string($value)) {
            $value = Zend_Json::decode($value);
        }
        if (!is_array($value)) $value = array();
        $row->{$this->_fieldPrefix.'dimension'} = isset($value['dimension']) ? $value['dimension'] : null;
        $row->{$this->_fieldPrefix.'width'} = (isset($value['width']) && (int)$value['width']) ? $value['width'] : null;
        $row->{$this->_fieldPrefix.'height'} = (isset($value['height']) && $value['height']) ? $value['height'] : null;
        if (isset($value['cropData'])) {
            $row->{$this->_fieldPrefix.'crop_x'} = (isset($value['cropData']['x']) && $value['cropData']['x'] !== null)
                ? $value['cropData']['x'] : null;
            $row->{$this->_fieldPrefix.'crop_y'} = (isset($value['cropData']['y']) && $value['cropData']['y'] !== null)
                ? $value['cropData']['y'] : null;
            $row->{$this->_fieldPrefix.'crop_width'} = (isset($value['cropData']['width']) && $value['cropData']['width'])
                ? $value['cropData']['width'] : null;
            $row->{$this->_fieldPrefix.'crop_height'} = (isset($value['cropData']['height']) && $value['cropData']['height'])
                ? $value['cropData']['height'] : null;
        } else {
            $row->{$this->_fieldPrefix.'crop_x'} = null;
            $row->{$this->_fieldPrefix.'crop_y'} = null;
            $row->{$this->_fieldPrefix.'crop_width'} = null;
            $row->{$this->_fieldPrefix.'crop_height'} = null;
        }
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = null;
        return $postData[$fieldName];
    }

    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        $data = $this->_getValueFromPostData($postData);
        if (!is_string($data)) {
            return $ret;
        }
        $data = Zend_Json::decode($data);
        $dimensions = $this->getDimensions();
        reset($dimensions);

        if ($this->getAllowBlank() === false
            || $this->getAllowBlank() === 0
            || $this->getAllowBlank() === '0') {
            if (!isset($dimensions[$data['dimension']])) {
                $ret[] = array(
                    'message' => trlKwf("Please fill out the field"),
                    'field' => $this
                );
            }
        }

        if (!empty($data['dimension'])) {
            $dimension = $dimensions[$data['dimension']];
        } else {
            $dimension = current($dimensions);
        }

        return $ret;
    }
}
