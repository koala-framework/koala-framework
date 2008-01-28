<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
abstract class Vpc_Paragraphs_Abstract extends Vpc_Abstract
{
    protected $_paragraphs;
    private $_rows;

    public static function getSettings()
    {
        static $settings;
        if (!isset($settings)) {
            $settings = array_merge(parent::getSettings(), array(
                'componentName' => 'Paragraphs',
                'hideInParagraphs' => true,
                'tablename' => 'Vpc_Paragraphs_Model'
            ));
            $settings['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
            $settings['childComponentClasses'] =
                Vpc_Admin::getInstance('Vpc_Paragraphs_Abstract')
                ->getAvailableComponents('Vpc');
        }
        return $settings;
    }

    protected function _getRows()
    {
        if (!isset($this->_rows)) {
            $where = array();
            $where['component_id = ?'] = $this->getDbId();
            if (!$this->showInvisible()) {
                $where['visible = ?'] = 1;
            }
            $this->_rows = $this->getTable()->fetchAll($where, 'pos');
        }
        return $this->_rows;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = array();
        foreach ($this->getChildComponents() as $paragraph) {
            $ret['paragraphs'][] = $paragraph->getTemplateVars();
        }
        return $ret;
    }

    public function getChildComponents()
    {
        if (!isset($this->_paragraphs)) {
            $this->_paragraphs = array();
            foreach ($this->_getRows() as $row) {
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

}
