<?php
class Vps_Dao_Index extends Vps_Db_Table
{
    protected $_name = 'vps_index';
    protected $_rowClass = 'Vps_Dao_Row_Index';

    private static $_componentIds = array();

    public static function updateIndex($componentId)
    {
        static $hasIndex;
        if (is_null($hasIndex)) {
            $hasIndex = Zend_Registry::get('config')->hasIndex;
        }
        if ($hasIndex && !in_array($componentId, self::$_componentIds)) {
            self::$_componentIds[] = $componentId;
        }
    }

    public static function process()
    {
        if (!self::$_componentIds) return;
        $processedPageIds = array();
        $t = new Vps_Dao_Index();
        foreach (self::$_componentIds as $componentId) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($componentId);
            foreach ($components as $component) {
                $page = $component->getPage();
                if (!$page) continue; //zB eine Box die direkt unter der root liegt
                if (in_array($page->dbId, $processedPageIds)) continue;
                $processedPageIds[] = $page->dbId;

                $select = new Vps_Component_Select();
                $select->whereFlag('searchContent')
                       ->whereBox(false);
                $cc = $page->getRecursiveChildComponents($select);
                $content = '';
                foreach ($cc as $c) {
                    $content .= ' '.$c->getComponent()->getSearchContent();
                }
                $content = trim($content);
                $row = $t->find($page->dbId)->current();
                if (!$row) {
                    $row = $t->createRow();
                    $row->component_id = $page->dbId;
                }
                $row->text = $content;
                $row->save();
            }
        }

    }
}
