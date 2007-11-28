<?p
class Vpc_Formular_Select_Component extends Vpc_Formular_Field_Abstra

    protected $_settings = arra
        'name' => '
        'type' => 'radi
    
    protected $_tablename = 'Vpc_Formular_Select_Model
    const NAME = 'Formular.Select
    protected $_option

    public function getTemplateVars
   
        $return = parent::getTemplateVars(
        $return['options'] = $this->getOptions(
        $return['type'] = $this->getSetting('type'
        $return['name'] = $this->getSetting('name'
        $return['size'] = $this->getSetting('size'
        $return['template'] = 'Formular/Select.html
        return $retur
   

    public function getOptions
   
        if (!$this->_options)
            $table = $this->getTable('Vpc_Formular_Select_OptionsModel'
            $where = arra
                'page_id = ?' => $this->getDbId(
                'component_key = ?' => $this->getComponentKey
            
            $rows = $table->fetchAll($where
            $options = array(
            foreach ($rows as $row)
                $this->_options[] = arra
                    'value' => $row->i
                    'text' => $row->tex
                    'checked' => $row->checke
                    'id' => $row->
                
           
       

        return $this->_option
   

    public function processInput
   
        if (isset($_POST[$this->getSetting('name')]))
            foreach($this->getOptions() AS $key => $option)
                $this->_options[$key]['checked'] = $option['value'] == $_POST[$this->getSetting('name')
           
       
   

    public function validateField($mandator
   
        if($mandatory && !isset($_POST[$this->getSetting('name')])
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen
       
        return '
   
