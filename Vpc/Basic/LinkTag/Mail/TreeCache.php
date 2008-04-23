<?php
class Vpc_Basic_LinkTag_Mail_TreeCache extends Vpc_Basic_LinkTag_Abstract_TreeCache
{
    public function afterGenerate()
    {
        parent::afterGenerate();
        $db = $this->_cache->getAdapter();
        $class = $db->quote($this->_class);
        $sql = "UPDATE vps_tree_cache tc1
        SET url=(SELECT CONCAT('mailto:', mail,
                    IF(text OR subject, '?', ''),
                    IF(subject, CONCAT('subject=', subject), ''),
                    IF (subject AND text, '&', ''),
                    IF(text, CONCAT('body=', text), '')
                    )
                FROM vpc_basic_link_mail WHERE component_id=tc1.db_id),
            rel='',
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
