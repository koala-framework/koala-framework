<?php
class E3_Component_Paragraphs extends E3_Component_Abstract
{
    protected $_paragraphs;

    public function getTemplateVars($mode)
    {
        $this->_paragraphs = array();

        $rows = $this->_dao->getTable('E3_Dao_Paragraphs')
                    ->fetchParagraphs($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());

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

    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        $rows = $this->_dao->getTable('E3_Dao_Paragraphs')
                    ->fetchParagraphs($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
        $order = $request->getPost('order');
        if (!is_null($order)) {
            $orders = explode(";", $order);
            foreach($rows as $row) {
                if (in_array($row->component_id, $orders)) {
                    $row->nr = array_search($row->component_id, $orders)+1;
                    $row->save();
                }
            }
        }

        $ret = parent::saveFrontendEditing($request);
        $ret['html'] = false;
        return $ret;
    }
}
