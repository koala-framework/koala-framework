<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
abstract class Vpc_Paragraphs_Abstract extends Vpc_Abstract
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
    
    protected function addChildComponent($newcomponent)
    {
        return $this->_paragraphs[] = $newcomponent;
    }  
    
    
    private function _getParagraphs()
    {
        if (!isset($this->_paragraphs)) {
            $this->_paragraphs = array();
    
            $db = $this->_getTable()->getAdapter();
            $where = "page_id='" . $this->getDbId() . "'";
            $where .= " AND component_key='" . $this->getComponentKey() . "'";
            $rows = $this->_getTable()->fetchAll($where, 'pos');
    
            foreach($rows as $row) {
                $c = $this->createComponent($row->component_class, $row->id);
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

}
