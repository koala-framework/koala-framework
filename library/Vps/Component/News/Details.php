<?php
class Vps_Component_News_Details extends Vps_Component_Abstract
{
    private $_newsId;
    private $_content;
    public function setNewsId($id)
    {
        $this->_newsId = $id;
    }
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $componentModel = $this->getDao()->getTable('Vps_Dao_Components');

        $rows = $this->getDao()->getTable('Vps_Dao_News')->find($this->_newsId);
        $row = $rows->current();
        
        $componentClass = $componentModel->getComponentClass($row->component_id);
        $this->_content = new $componentClass($this->getDao(), $row->component_id);

        $ret['content'] = $this->_content->getTemplateVars($mode);
       	$ret['template'] = 'News/Details.html';
        return $ret;
    }
    public function getComponentInfo()
    {
    	return parent::getComponentInfo() + $this->_content->getComponentInfo();
    }
}