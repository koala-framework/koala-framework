<?php
class Kwc_NewsletterCategory_Update_20150309Legacy35002 extends Kwf_Update {
    public function update()
    {
        $db = Zend_Registry::get('db');
        if ($db->fetchOne("SHOW TABLES LIKE 'kwf_pools'")) {
            $db->query("INSERT INTO `kwc_newsletter_categories` (SELECT id, pos, value FROM kwf_pools WHERE pool='Newsletterkategorien')");
            $db->query("DELETE FROM kwf_pools WHERE pool='Newsletterkategorien'");
        }
    }
}
