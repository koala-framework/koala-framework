<?p
class Vps_Collection_FormFields extends Vps_Collecti

    private $_formName = nul

    public function __construct($name = nul
   
        $this->_formName = $nam
   

    protected function _postInsertValue($fiel
   
        if ($this->_formName)
            $field->setNamePrefix($this->_formName
       
   

    public function setFormName($nam
   
        $this->_formName = $nam
        foreach($this as $field)
            $this->_postInsertValue($field
       
   

    public function getFormName
   
        return $this->_formNam
   

