<?php
class Kwc_Basic_LinkTag_Intern_Update_20150309Legacy32519 extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("ALTER TABLE `kwc_basic_link_intern` ADD INDEX ( `target` )");
    }
}
