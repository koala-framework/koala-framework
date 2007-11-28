<?p
class Vpc_Formular_Textarea_Component extends Vpc_Formular_Field_Abstra

    public static function getSettings
   
        return array_merge(parent::getSettings(), arra
            'componentName' => 'Formular Fields.Textarea
            'tablename' => 'Vpc_Formular_Textarea_Model
            'default' => arra
                'width' => '150
                'height' => '50
                'name' => '
                'value' => 
           
        )
   

    function getTemplateVars
   
        $return = parent::getTemplateVars(
        $return['value'] = $this->_row->valu
        $return['width'] = $this->_row->widt
        $return['height'] = $this->_row->heigh
        if (isset($this->_row->name))
            $return['name'] = $this->_row->nam
        } else
            $return['name'] = $this->_store['name'
       
      
        return $retur
   
