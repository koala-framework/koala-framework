<?p
class Vps_Auto_Field_TextField extends Vps_Auto_Field_SimpleAbstra

    public function __construct($field_name = null, $field_label = nul
   
        parent::__construct($field_name, $field_label
        $this->setXtype('textfield'
   

    protected function _addValidators
   
        parent::_addValidators(

        if ($this->getVType() === 'email')
            $this->addValidator(new Zend_Validate_EmailAddress()
        } else if ($this->getVType() === 'url')
            //todo, reuse Zend_Uri::che
        } else if ($this->getVType() === 'alpha')
            $this->addValidator(new Zend_Validate_Alpha()
        } else if ($this->getVType() === 'alphanum')
            $this->addValidator(new Zend_Validate_Alnum()
       
   

