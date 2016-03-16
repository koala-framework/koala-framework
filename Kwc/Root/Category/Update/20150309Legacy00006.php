<?php
class Kwc_Root_Category_Update_20150309Legacy00006 extends Kwf_Update
{
    public function update()
    {
        parent::update();
        $db = Zend_Registry::get('db');

        $r = $db->fetchOne("SHOW FIELDS FROM kwf_pages LIKE 'tags'");
        if ($r) $db->query("ALTER TABLE kwf_pages DROP tags");

        $r = $db->fetchOne("SHOW FIELDS FROM kwf_pages LIKE 'domain'");
        if ($r) $db->query("ALTER TABLE kwf_pages DROP domain");
    }
}
