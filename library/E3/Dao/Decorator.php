<?php
class E3_Dao_Decorator extends Zend_Db_Table
{
    protected $_name = 'component_decorator';
    protected $_primary = array('parent_component_id', 'parent_page_key', 'parent_component_key');
}