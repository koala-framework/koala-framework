<?p
class Vpc_Formular_Submit_Component extends Vpc_Abstra

    public static function getSettings
   
        return array_merge(parent::getSettings(), arra
            'componentName' => 'Formular Fields.Submit
            'tablename' => 'Vpc_Formular_Submit_Model
            'default' => arra
                'name' => 'submit
                'text' => 'Submi
           
        )
   

    function getTemplateVars
   
        $return = parent::getTemplateVars(
        $return['text'] = $this->_row->tex
        if (isset($this->_row->name))
            $return['name'] = $this->_row->nam
        } else
            $return['name'] = $this->_store['name'
       
        return $retur
   
