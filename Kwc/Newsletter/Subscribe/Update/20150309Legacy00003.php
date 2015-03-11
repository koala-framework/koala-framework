<?php
class Kwc_Newsletter_Subscribe_Update_20150309Legacy00003 extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');

        //drop unique index (if exists)
        $sql = "ALTER TABLE `kwc_newsletter_subscribers` DROP INDEX `email`";
        try {
            $db->query($sql);
        } catch (Exception $e) {}

        //drop unique index (if exists)
        $sql = "ALTER TABLE  `kwc_newsletter_subscribers` DROP INDEX  `email_2`";
        try {
            $db->query($sql);
        } catch (Exception $e) {}

        $sql = "ALTER TABLE `kwc_newsletter_subscribers` ADD INDEX `email` ( `email` )";
        $db->query($sql);
    }
}
