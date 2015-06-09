<?php
class Kwf_Update_20150309Legacy38003 extends Kwf_Update
{
    public function getTags()
    {
        return array('fulltext');
    }

    public function update()
    {
        $db = Kwf_Registry::get('db');
        if (!$db->fetchOne("SHOW FIELDS FROM kwc_fulltext_meta LIKE 'changed_recursive'")) {
            $db->query('ALTER TABLE  `kwc_fulltext_meta` ADD  `changed_recursive` TINYINT NOT NULL');
        }
    }
}
