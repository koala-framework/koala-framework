<?php
class Vpc_News_Details extends Vpc_Abstract
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

        $rows = $this->getDao()->getTable('Vps_Dao_News')->find($this->_newsId);
        $row = $rows->current();
        
        $this->_content = $this->createComponent('', $row->component_id);

        $ret['content'] = $this->_content->getTemplateVars($mode);
        $ret['template'] = 'News/Details.html';
        return $ret;
    }
    
    public function getComponentInfo()
    {
        return parent::getComponentInfo() + $this->_content->getComponentInfo();
    }
}