<?php
class Vpc_NewsletterCategory_Update_35002 extends Vps_Update {
    public function update()
    {
        $db = Zend_Registry::get('db');
        if ($db->fetchOne("SHOW TABLES LIKE 'vps_pools'")) {
            $db->query("INSERT INTO `vpc_newsletter_categories` (SELECT id, pos, value FROM vps_pools WHERE pool='Newsletterkategorien')");
            $db->query("DELETE FROM vps_pools WHERE pool='Newsletterkategorien'");
        }
    }
}
