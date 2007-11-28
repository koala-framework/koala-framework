<?p
class Vps_Collection_Iterator_RecursiveFormFields extends Vps_Collection_Iterator_Recursi

    public function hasChildren
   
        if($this->current() instanceof Vps_Auto_Field_Abstract)
            return $this->current()->hasChildren(
        } else
            return fals
       
   

    public function getChildren
   
        return new Vps_Collection_Iterator_Recursive($this->current()->getChildren()
   

