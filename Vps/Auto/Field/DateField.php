<?p
class Vps_Auto_Field_DateField extends Vps_Auto_Field_SimpleAbstra

    public function __construct($field_name = null, $field_label = nul
   
        parent::__construct($field_name, $field_label
        $this->setXtype('datefield'
   

    protected function _addValidators
   
        parent::_addValidators(
        $this->addValidator(new Zend_Validate_Date()
   

    protected function _getValueFromPostData($postDat
   
        $ret = parent::_getValueFromPostData($postData
        if ($ret == '') $ret = nul
        return $re
   

