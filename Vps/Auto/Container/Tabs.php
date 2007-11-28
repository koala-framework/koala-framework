<?p
class Vps_Auto_Container_Tabs extends Vps_Auto_Container_Abstra

    public $tab

    public function __construct($name = nul
   
        parent::__construct($name
        $this->tabs = new Vps_Collection('Vps_Auto_Container_Tab'
        $this->setDeferredRender(false); //verursacht combobox-view-breite-b
        $this->setBaseCls('x-plain'
        $this->setXtype('tabpanel'
   

    public function getMetaData
   
        $ret = parent::getMetaData(
        $ret['items'] = $this->tabs->getMetaData(
        return $re
   

    public function getByName($nam
   
        $ret = parent::getByName($name
        if($ret) return $re
        return $this->tabs->getByName($name
   
    public function hasChildren
   
        return sizeof($this->tabs) > 
   
    public function getChildren
   
        return $this->tab
   

    public function add($v = nul
   
        $return = $this->tabs->add($v
        $return->setTitle($v
        return $retur
   

