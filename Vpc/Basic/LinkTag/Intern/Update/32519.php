<?php
class Vpc_Basic_LinkTag_Intern_Update_32519 extends Vps_Update
{
    protected $_tags = array('vpc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("ALTER TABLE `vpc_basic_link_intern` ADD INDEX ( `target` )");
    }
}
