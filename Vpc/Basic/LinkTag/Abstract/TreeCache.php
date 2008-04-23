<?php
class Vpc_Basic_LinkTag_Abstract_TreeCache extends Vpc_TreeCache_Abstract implements Vpc_TreeCache_AfterGenerate_Interface
{
    protected $_loadTableFromComponent = true;

    public function afterGenerate()
    {
    }

    public function onUpdateRow($row)
    {
        parent::onUpdateRow($row);
        if ($row->getTable() instanceof $this->_table) {

            //1. link updaten
            $where = array('db_id = ?' => $row->component_id);
            foreach ($this->_cache->fetchAll($where) as $tcRow) {
                $this->_updateLink($tcRow);
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

    //möglicherweise ineffiziente standardimplementierung
    protected function _updateLink($tcRow)
    {
        $tcRow->generated = Vps_Dao_TreeCache::GENERATE_AFTER;
        $tcRow->save();
        $this->afterGenerate();
    }
}
