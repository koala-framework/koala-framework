<?php
class Vps_Form_Field_TimeField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('timefield');
        $this->setFormat('H:i');
        $this->setWidth(70);
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Vps_Validate_Time());
    }

    public function load($row, $postData = array())
    {
        $ret = array();
        if (isset($postData[$this->getFiledName()])) {
            $ret[$this->getFieldName()] = $postData[$this->getFiledName()];
        } else {
            $v = $this->getData()->load($row);
            if (strlen($v) > 5) {
                $v = substr($v, 0, 5);
            }
            $ret[$this->getFieldName()] = $v;
        }
        return array_merge($ret, Vps_Form_Field_Abstract::load($row, $postData));
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '') $ret = null;
        return $ret;
    }
}
