<?php
class Vpc_Basic_LinkTag_FirstChildPage_TreeCache extends Vpc_TreeCache_Abstract implements Vpc_TreeCache_AfterGenerate_Interface
{
    public function afterGenerate()
    {
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
            ->from('vps_tree_cache', array('url', 'rel', 'component_class', 'generated'))
            ->where('parent_component_id=?', $tcRow->component_id)
            ->order('pos')
            ->limit(1);

        $r = $select->query()->fetchAll();
        if (count($r)) {
            $r = $r[0];
            if ($r['generated'] == Vps_Dao_TreeCache::GENERATE_AFTER) {
                $a = Vpc_TreeCache_Abstract::getInstance($r['component_class']);
                if ($a) $a->afterGenerate();
                $r = $select->query()->fetchAll();
                $r = $r[0];
            }
            $tcRow->url_preview = $r['url'];
            $tcRow->rel_preview = $r['rel'];
        } else {
            $tcRow->url_preview = null;
            $tcRow->rel_preview = null;
        }

        $select->where('visible=1');
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
        } else {
            $tcRow->url = null;
            $tcRow->rel = null;
        }
        $tcRow->url_match = null;
        $tcRow->url_match_preview = null;
        $tcRow->url_pattern = null;
        $tcRow->generated = Vps_Dao_TreeCache::GENERATE_FINISHED;
        $tcRow->save();
    }
}
