<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
abstract class Vpc_Paragraphs_Abstract extends Vpc_Abstract
{
    protected $_paragraphs = array();
    protected static $_settings;

    public static function getSettings()
    {
        if (!self::$_settings) {
            $ret = array_merge(parent::getSettings(), array(
                'componentName' => 'Paragraphs',
                'hideInParagraphs' => true,
                'tablename' => 'Vpc_Paragraphs_Model'
            ));
            $ret['childComponentClasses'] = Vpc_Admin::getInstance('Vpc_Paragraphs_Abstract')
                                    ->getComponents();
            self::$_settings = $ret;
        }
        return self::$_settings;
    }

    protected function _init()
    {
        $where = array();
        $where['page_id = ?'] = $this->getDbId();
        $where['component_key = ?'] = $this->getComponentKey();
        if (!$this->showInvisible()) {
            $where['visible = ?'] = 1;
        }
        $rows = $this->getTable()->fetchAll($where, 'pos');
        foreach($rows as $row) {
            $c = $this->createComponent($row->component_class, $row->id);
            $this->_paragraphs[$row->id] = $c;
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = array();
        foreach($this->getChildComponents() as $paragraph) {
            $ret['paragraphs'][] = $paragraph->getTemplateVars();
        }
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
