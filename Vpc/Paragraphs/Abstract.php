<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
abstract class Vpc_Paragraphs_Abstract extends Vpc_Abstract
{
    protected $_data;
    protected $_paragraphs;
    protected $_tablename = 'Vpc_Paragraphs_IndexModel';
    const NAME = 'Paragraphs';

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = array();
        foreach($this->getChildComponents() as $paragraph) {
            $vars = $paragraph->getTemplateVars();
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
        foreach ($this->getChildComponents() as $p) {
            $p->generateHierarchy($filename);
        }
    }

    public function getChildComponents()
    {
        if (!isset($this->_paragraphs)) {
            $this->_paragraphs = array();
            foreach($this->_getData() as $row) {
                $c = $this->createComponent($row->component_class, $row->id);
                $this->_paragraphs[$row->id] = $c;
            }
        }
        return $this->_paragraphs;
    }

    public function getChildComponent($id)
    {
        $childComponents = $this->getChildComponents();
        if (isset($childComponents[$id])) {
            return $childComponents[$id];
        } else {
            return null;
        }
    }

    protected function _getData()
    {
        if (!isset($this->_data)) {
            $where = array();
            $where['page_id = ?'] = $this->getDbId();
            $where['component_key = ?'] = $this->getComponentKey();
            if (!$this->showInvisible()) {
                $where['visible = ?'] = 1;
            }
            $this->_data = $this->getTable()->fetchAll($where, 'pos');
        }
        return $this->_data;
    }

}
