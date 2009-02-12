<?php
class Vps_Update_24709 extends Vps_Update
{
    public function update()
    {
        parent::update();
        $db = Zend_Registry::get('db');
        $db->query(" ALTER TABLE `cache_users` DROP INDEX `email_2` ,
ADD INDEX `email_2` ( `email` , `webcode` , `deleted` ) ");

    }
}
