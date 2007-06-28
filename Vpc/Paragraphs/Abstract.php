<?php
class Vpc_Paragraphs_Abstract extends Vpc_Abstract
{
    protected $_paragraphs;

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['paragraphs'] = array();
        foreach($this->_getParagraphs() as $paragraph) {
            $vars = $paragraph->getTemplateVars($mode);
            if (isset($vars['template'])) {
                $ret['paragraphs'][] = $vars;
            }
        }
        $ret['template'] = 'Paragraphs.html';
        return $ret;
    }

    public function generateHierarchy($filename = '')
    {
        parent::generateHierarchy($filename);
        foreach ($this->_getParagraphs() as $p) {
            $p->generateHierarchy($filename);
        }
    }

    public function getChildComponents()
    {
        return $this->_getParagraphs();
    }
    
    private function _getParagraphs()
    {
        if (!isset($this->_paragraphs)) {
            $this->_paragraphs = array();
    
            $rows = $this->_dao->getTable('Vps_Dao_Paragraphs')
                        ->fetchParagraphs($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
    
            foreach($rows as $row) {
                $c = $this->createComponent('', $row->component_id);
                $this->_paragraphs[] = $c;
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

}
