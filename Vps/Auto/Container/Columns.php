<?p
class Vps_Auto_Container_Columns extends Vps_Auto_Container_Abstra

    public $column

    public function __construct($name = nul
   
        $this->columns = new Vps_Collection('Vps_Auto_Container_Column'
        parent::__construct($name
   

    public function getMetaData
   
        $ret = parent::getMetaData(
        $ret['items'] = $this->columns->getMetaData(
        if (!isset($ret['layout'])) $ret['layout'] = 'column
        if (!isset($ret['border'])) $ret['border'] = fals
        if (!isset($ret['baseCls'])) $ret['baseCls'] = 'x-plain
        return $re
   

    public function getByName($nam
   
        $ret = parent::getByName($name
        if($ret) return $re
        return $this->columns->getByName($name
   
    public function hasChildren
   
        return sizeof($this->columns) > 
   
    public function getChildren
   
        return $this->column
   

    public function add($v = nul
   
        return $this->columns->add($v
   

