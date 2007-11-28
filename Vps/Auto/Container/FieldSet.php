<?p
class Vps_Auto_Container_FieldSet extends Vps_Auto_Container_Abstra

    public function __construct($title = nul
   
        parent::__construct(
        $this->setTitle($title
        $this->setAutoHeight(true
        $this->setXtype('fieldset'
   

    public function setCheckboxName($nam
   
        $this->fields->add(new Vps_Auto_Field_Hidden($name)
        return $this->setProperty('checkboxName', $name
   

