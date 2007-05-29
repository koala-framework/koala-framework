<?php
class Vpc_Paragraphs_Index extends Vpc_Abstract
{
    protected $_paragraphs;

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['paragraphs'] = array();
        foreach($this->_getParagraphs() as $paragraph) {
            $ret['paragraphs'][] = $paragraph->getTemplateVars($mode);
        }
        $ret['template'] = 'Paragraphs.html';
        return $ret;
    }
    
    private function _getParagraphs()
    {
        if (!isset($this->_paragraphs)) {
            $this->_paragraphs = array();
    
            $rows = $this->_dao->getTable('Vps_Dao_Paragraphs')
                        ->fetchParagraphs($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
    
            foreach($rows as $row) {
                $this->_paragraphs[] = $this->createComponent('', $row->component_id);
            }
        }
        return $this->_paragraphs;
    }

    public function getComponentInfo()
    {
      $info = parent::getComponentInfo();
      foreach ($this->_getParagraphs() as $p) {
        $info += $p->getComponentInfo();
      }
      return $info;
    }

    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        $rows = $this->_dao->getTable('Vps_Dao_Paragraphs')
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

    public function getChildComponents()
    {
        return $this->_getParagraphs();
    }
}
