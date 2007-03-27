<?php
class E3_Component_Paragraphs extends E3_Component_Abstract
{
    protected $_paragraphs;

    public function getTemplateVars($mode)
    {
        $this->_paragraphs = array();

        $rows = $this->_dao->getTable('E3_Dao_Paragraphs')
            ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());

        $componentModel = $this->_dao->getTable('E3_Dao_Components');
        foreach($rows as $row) {
            $componentClass = $componentModel->getComponentClass($row->component_id);
            $this->_paragraphs[] = new $componentClass($this->_dao, $row->component_id);
        }

        $ret = parent::getTemplateVars($mode);
        $ret['paragraphs'] = array();
        foreach($this->_paragraphs as $paragraph) {
            $ret['paragraphs'][] = $paragraph->getTemplateVars($mode);
        }
        $ret['template'] = 'Paragraphs.html';
        return $ret;
    }

    public function getComponentInfo()
    {
    	$info = parent::getComponentInfo();
    	foreach ($this->_paragraphs as $p) {
    		$info += $p->getComponentInfo();
    	}
    	return $info;
    }
    
}
