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
        $settings = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Paragraphs'),
            'componentIcon' => new Vps_Asset('page')
        ));
        $settings['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $settings['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
        $settings['childComponentClasses']['image'] = 'Vpc_Basic_Image_Component';
        return $settings;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $tc = $this->getTreeCacheRow()->getTable();
        $select = $tc->select();
        $select->from('vps_tree_cache');
        $select->where('vps_tree_cache.parent_component_id = ?', $this->getComponentId());
        if (!$this->_showInvisible()) {
            $select->where('vps_tree_cache.visible = 1');
        }
        $select->join('vpc_paragraphs', "CONCAT(vpc_paragraphs.component_id, '-', vpc_paragraphs.id) = vps_tree_cache.db_id", array());
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
