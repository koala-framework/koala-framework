<?p
class Vpc_Formular_Textbox_Component extends Vpc_Formular_Field_Abstra

    public static function getSettings
   
        return array_merge(parent::getSettings(), arra
            'componentName' => 'Formular Fields.Textbox
            'tablename' => 'Vpc_Formular_Textbox_Model
            'default' => arra
                'maxlength' => '255
                'width' => '150
                'name' => '
                'value' => '
                'validator' => 
           
        )
   

    public function getTemplateVars
   
        $return = parent::getTemplateVars(
        $return['value'] = $this->_row->valu
        $return['maxlength'] = $this->_row->maxlengt
        $return['width'] = $this->_row->widt
        $return['name'] = $this->_getName(
        return $retur
   

    protected function _getName
   
        if (isset($this->_row->name))
            //subotim
            return $this->_row->nam
        } else
            return $this->_store['name'
       
   

    public function processInput
   
        $name = $this->_getName(
        if (isset($_POST[$name])
            $value = $_POST[$name
       
        $this->_row->value = $valu
   

    public function validateField($mandator
   
        $value = $this->_row->valu
        $validatorString = $this->_row->validato
        if ($validatorString != '' && $value != ''
            $validator = new $validatorString(
            if (!$validator->isValid($value))
                $v = str_replace('Zend_Validate_', '', $validatorString
                return 'Das Feld ' . $this->getStore('fieldLabel') . ' entspricht nicht der geforderten Formatierung (' . $v . ')
           
       
        if($mandatory && $value == ''
            return 'Feld ' . $this->getStore('fieldLabel') . ' ist ein Pflichtfeld, bitte ausfüllen
       
        return '
   
