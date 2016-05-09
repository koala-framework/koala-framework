<?php
class Kwc_Mail_Update_20160407AddReturnPathColumn extends Kwf_Update
{
    public function update()
    {
        $ret = parent::update();
        $mailRow = Kwf_Model_Abstract::getInstance('Kwc_Mail_Model')->createRow();
        if (!isset($mailRow->return_path)) {
            Kwf_Registry::get('db')->query("ALTER TABLE `kwc_mail` ADD `return_path` VARCHAR( 255 ) NOT NULL");
        }
        return $ret;
    }
}
