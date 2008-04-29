<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
class Vpc_Paragraphs_Component extends Vpc_Abstract
{
    private $_rows;

    public static function getSettings()
    {
        static $settings;
        if (!isset($settings)) {
            $settings = array_merge(parent::getSettings(), array(
                'componentName' => trlVps('Paragraphs'),
                'componentIcon' => new Vps_Asset('page'),
                'hideInParagraphs' => true
            ));
            $settings['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
            $settings['childComponentClasses'] = 
                Vpc_Admin::getInstance('Vpc_Paragraphs_Component')
                ->getAvailableComponents('Vpc');

        }
        return $settings;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $tc = $this->getTreeCacheRow()->getTable();
        $select = $tc->select();
        $select->from('vps_tree_cache');
        $select->where('vps_tree_cache.parent_component_id = ?', $this->getDbID());
        if (!$this->_showInvisible()) {
            $select->where('vps_tree_cache.visible = 1');
        }
        $select->join('vpc_paragraphs', "CONCAT(vpc_paragraphs.component_id, '-', vpc_paragraphs.id) = vps_tree_cache.component_id", array());
        $select->where('parent_component_class = ?', get_class($this));
        $select->where('vpc_paragraphs.component_id = ?', $this->getDbID());
        
        $select->order('vps_tree_cache.pos');
        $ret['paragraphs'] = array();
        foreach ($tc->fetchAll($select) as $row) {
            $ret['paragraphs'][] = $row->component_id;
        }

        return $ret;
    }
}
