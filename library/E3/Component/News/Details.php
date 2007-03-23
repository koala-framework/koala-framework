<?php
class E3_Component_News_Details extends E3_Component_Abstract
{
    private $_newsId;
    private $_content;
    public function setNewsId($id)
    {
        $this->_newsId = $id;
    }
    public function getTemplateVars()
    {
        $componentModel = $this->getDao()->getTable('E3_Dao_Components');

        $rows = $this->getDao()->getTable('E3_Dao_News')->find($this->_newsId);
        $row = $rows->current();
        
        $componentClass = $componentModel->getComponentClass($row->component_id);
        $this->_content = new $componentClass($this->getDao(), $row->component_id);

        $ret['content'] = $this->_content->getTemplateVars();
       	$ret['template'] = 'News/Details.html';
        return $ret;
    }
    public function getComponentInfo()
    {
    	return parent::getComponentInfo() + $this->_content->getComponentInfo();
    }
}