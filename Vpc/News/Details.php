<?p
class Vpc_News_Details extends Vpc_Abstra

    private $_i
    public $titl
    private $_conten
  
    public function setNewsId($i
   
        $this->_id = $i
   
  
    public function getTemplateVars
   
        $ret = parent::getTemplateVars(

        $rows = $this->getDao()->getTable('Vps_Dao_News')->find($this->_newsId
        $row = $rows->current(
      
        $this->_content = $this->createComponent('', $row->component_id

        $ret['content'] = $this->_content->getTemplateVars(
        $ret['template'] = 'News/Details.html
        return $re
   
  
    public function getComponentInfo
   
        return parent::getComponentInfo() + $this->_content->getComponentInfo(
   
  
