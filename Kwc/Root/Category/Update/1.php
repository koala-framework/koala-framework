<?php
class Vpc_Root_Category_Update_1 extends Vps_Update
{
    public function update()
    {
        parent::update();
        $db = Zend_Registry::get('db');

        $r = $db->fetchOne("SHOW FIELDS FROM vps_pages LIKE 'tags'");
        if ($r) $db->query("ALTER TABLE vps_pages DROP tags");

        $r = $db->fetchOne("SHOW FIELDS FROM vps_pages LIKE 'domain'");
        if ($r) $db->query("ALTER TABLE vps_pages DROP domain");
    }
}
