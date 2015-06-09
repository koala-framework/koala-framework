<?php
class Kwc_Newsletter_Update_20150309Legacy34701 extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');

        //drop any indizies that might exist on email
        try {
            $db->query("ALTER TABLE  `kwc_newsletter_subscribers` DROP INDEX  `email_2`");
        } catch (Exception $e) {}
        try {
            $db->query("ALTER TABLE  `kwc_newsletter_subscribers` DROP INDEX  `email`;");
        } catch (Exception $e) {}

        //add proper index
        $db->query("ALTER TABLE  `kwc_newsletter_subscribers` ADD UNIQUE `email` (`email`)");
    }
}
