<?php
class E3_Dao_Textbox extends Zend_Db_Table
{
    protected $_name = 'component_textbox';
    protected $_primary = array('component_id', 'page_key', 'component_key');
}