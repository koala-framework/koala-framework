<?php
class Vpc_Basic_LinkTag_Intern_TreeCache extends Vpc_Basic_LinkTag_Abstract_TreeCache
{
    public function afterGenerate()
    {
        parent::afterGenerate();
        $where = array(
            'generated = ?' => Vps_Dao_TreeCache::GENERATE_AFTER,
            'component_class = ?' => $this->_class
        );
        foreach ($this->_cache->fetchAll($where) as $row) {
            $this->_updateLink($row);
        }
    }

    protected function _updateLink($tcRow)
    {
        //parent:_updateLink nicht aufrufen!

        $tcRow->generated = Vps_Dao_TreeCache::GENERATE_AFTER_START;
        $tcRow->save();

        $select = $this->_cache->getAdapter()->select()
            ->from('vpc_basic_link_intern', array())
            ->joinLeft('vps_tree_cache',
                    'vpc_basic_link_intern.target=vps_tree_cache.db_Id',
                    array('url', 'rel', 'generated', 'component_class'))
            ->where('vpc_basic_link_intern.component_id=?', $tcRow->db_id);
        $r = $select->query()->fetchAll();
        if (count($r)) {
            $r = $r[0];
            if ($r['generated'] == Vps_Dao_TreeCache::GENERATE_AFTER) {
                $a = Vpc_TreeCache_Abstract::getInstance($r['component_class']);
                if ($a) $a->afterGenerate();
                $r = $select->query()->fetchAll();
                $r = $r[0];
            }
            $tcRow->url = $r['url'];
            $tcRow->rel = $r['rel'];
            $tcRow->url_preview = $r['url'];
            $tcRow->rel_preview = $r['rel'];
        } else {
            $tcRow->url = null;
            $tcRow->rel = null;
            $tcRow->url_preview = null;
            $tcRow->rel_preview = null;
        }
        $tcRow->url_match = null;
        $tcRow->url_match_preview = null;
        $tcRow->url_pattern = null;
        $tcRow->generated = Vps_Dao_TreeCache::GENERATE_FINISHED;
        $tcRow->save();
    }
}
