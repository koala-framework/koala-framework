<?php
class Kwf_Update_20150309Legacy38016 extends Kwf_Update
{
    protected $_tags = array('kwf');

    public function update()
    {
        $db = Zend_Registry::get('db');
        $sql = $db->query('SHOW COLUMNS FROM kwf_users');
        $fields = $sql->fetchAll();
        //don't do the change password always
        //it can happen that the kwf_user table doesn't have a kwf_users column
        //e.g. user service -> table cache_users
        foreach($fields as $field){
            if ($field['Field'] == 'password') {
                $sql = 'ALTER TABLE `kwf_users` CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
                $db->query($sql);
            }
        }
    }
}
