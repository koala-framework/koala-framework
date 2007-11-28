<?p
/
 * Basisklasse für Fields die andere Fields beinhalt

 * zB FieldS
 *
abstract class Vps_Auto_Container_Abstract extends Vps_Auto_Field_Abstra

    public $field

    public function __construct($name = nul
   
        parent::__construct($name
        $this->fields = new Vps_Collection(
   

    public function getMetaData
   
        $iterator = new RecursiveIteratorIterator(new Vps_Collection_Iterator_Recursive($this->fields)
        foreach ($iterator as $field)
            if ($field instanceof Vps_Auto_Field_File)
                $this->setFileUpload(true
           
            if ($field instanceof Vps_Auto_Field_Fi
                || $field instanceof Vps_Auto_Field_HtmlEditor)
                $this->setLoadAfterSave(true
           
       
        $ret = parent::getMetaData(
        $ret['name'] = $this->getName(
        $ret['items'] = $this->fields->getMetaData(
        return $re
   

    public function getByName($nam
   
        $ret = parent::getByName($name
        if($ret) return $re
        return $this->fields->getByName($name
   

    public function hasChildren
   
        return sizeof($this->fields) > 
   
    public function getChildren
   
        return $this->field
   

    public function add($v = nul
   
        return $this->fields->add($v
   

