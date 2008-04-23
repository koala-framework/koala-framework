<?php
class Vpc_Basic_LinkTag_Extern_TreeCache extends Vpc_Basic_LinkTag_Abstract_TreeCache
{
    public function afterGenerate()
    {
        parent::afterGenerate();
        $db = $this->_cache->getAdapter();

        $sql = "UPDATE vps_tree_cache tc1
        SET url=(SELECT target FROM vpc_basic_link_extern
                            WHERE component_id=tc1.db_id),
            rel=(SELECT IF(is_popup, CONCAT(
                IF(width, CONCAT('width=', width, ','), ''),
                IF(height, CONCAT('height=', height, ','), ''),
                'menubar=', IF(menubar, 'yes', 'no'),
                ',toolbar=', IF(toolbar, 'yes', 'no'),
                ',location=', IF(locationbar, 'yes', 'no'),
                ',status=', IF(statusbar, 'yes', 'no'),
                ',scrollbars=', IF(scrollbars, 'yes', 'no'),
                ',resizable=', IF(resizeable, 'yes', 'no')
                ), '')
                FROM vpc_basic_link_extern WHERE component_id=tc1.db_id),
            url_pattern = NULL, url_match=NULL, url_match_preview=NULL
        WHERE component_class=:class AND generated=:generated";
        $data = array();
        $data['generated'] = Vps_Dao_TreeCache::GENERATE_AFTER;
        $data['class'] = $this->_class;
        $this->_loggedQuery($sql, $data);

        $sql = "UPDATE vps_tree_cache
                SET rel_preview=rel, url_preview=url, generated=:set_generated
                WHERE component_class=:class AND generated=:generated";
        $data = array();
        $data['set_generated'] = Vps_Dao_TreeCache::GENERATE_FINISHED;
        $data['generated'] = Vps_Dao_TreeCache::GENERATE_AFTER;
        $data['class'] = $this->_class;
        $this->_loggedQuery($sql, $data);
    }
}
