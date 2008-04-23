<?php
class Vpc_Basic_LinkTag_TreeCache extends Vpc_TreeCache_Table implements Vpc_TreeCache_AfterGenerate_Interface
{
    protected $_componentClass = 'link_class';

    protected function _getSelectFields()
    {
        $fields = parent::_getSelectFields();

        $sql = "CONCAT(tc.component_id, '-1')";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        $sql = "CONCAT(tc.db_id, '-1')";
        $fields['db_id'] = new Zend_Db_Expr($sql);

        return $fields;
    }

    public function afterGenerate()
    {
        $select = $this->_cache->getAdapter()->select();
        $select->from('vps_tree_cache', array('component_class', 'component_id', 'generated'))
               ->group('component_class')
               ->where('generated = ?', Vps_Dao_TreeCache::GENERATE_AFTER)
               ->where("component_id = CONCAT(parent_component_id, '-1')")
               ->where('(SELECT component_class FROM vps_tree_cache p WHERE p.component_id=vps_tree_cache.parent_component_id)=?', $this->_class);
        foreach ($select->query()->fetchAll() as $row) {
            $a = Vpc_TreeCache_Abstract::getInstance($row['component_class']);
            if ($a) $a->afterGenerate();
        }

        $sql = "UPDATE vps_tree_cache tc1
        LEFT JOIN vps_tree_cache tc2 ON tc2.component_id=CONCAT(tc1.component_id, '-1')
            SET tc1.url=tc2.url,
                tc1.url_preview=tc2.url_preview,
                tc1.rel=tc2.rel,
                tc1.rel_preview=tc2.rel_preview,
                tc1.generated=:set_generated,
                tc1.url_pattern=NULL,
                tc1.url_match=NULL,
                tc1.url_match_preview=NULL
        WHERE tc1.component_class=:class AND tc1.generated=:generated
        ";
        $data = array(
            'class'=>$this->_class,
            'set_generated'=>Vps_Dao_TreeCache::GENERATE_FINISHED,
            'generated'=>Vps_Dao_TreeCache::GENERATE_AFTER
        );
        $this->_loggedQuery($sql, $data);
    }

    public function onDeleteRow(Vps_Db_Table_Row_Abstract $row)
    {
        if ($row->getTable() instanceof $this->_table) {
            //admin erledigt den rest
            $db = $this->_table->getAdapter();
            $where = array();
            $i = $this->_table->info();
            $primaryKey = $i['primary'][1]; //TODO: mehrere primary keys
            $where[] = $db->quoteInto('db_id = ?', $row->component_id.'-1');
            $where[] = $db->quoteInto('parent_component_class = ?', $this->_class);
            foreach ($this->_cache->fetchAll($where) as $tcRow) {
                $tcRow->delete();
            }
        }
        Vpc_TreeCache_Abstract::onDeleteRow($row);
    }

    public function onUpdateRow($row)
    {
        parent::onUpdateRow($row);

        if ($row->getTable() instanceof $this->_table) {

            //1. link updaten
            $where = array('db_id = ?' => $row->component_id);
            foreach ($this->_cache->fetchAll($where) as $tcRow) {
                $tcRow->generated = Vps_Dao_TreeCache::GENERATE_AFTER;
                $tcRow->save();
                $this->afterGenerate();
            }

            //2. wenn eine parentseite auch afterGenerate hat dieses aufrufen
            //zB für ersteUnterseite oder LinkTag
            $where = array('db_id = ?' => $row->component_id);
            foreach ($this->_cache->fetchAll($where) as $tcRow) {
                while ($tcRow = $tcRow->findParentPage()) {
                    $tc = Vpc_TreeCache_Abstract::getInstance($tcRow->component_class);
                    if ($tc && $tc instanceof Vpc_TreeCache_AfterGenerate_Interface) {
                        $tcRow->generated = Vps_Dao_TreeCache::GENERATE_AFTER;
                        $tcRow->save();
                        $tc->afterGenerate();
                    } else {
                        break;
                    }
                }
            }
        }
    }
}
