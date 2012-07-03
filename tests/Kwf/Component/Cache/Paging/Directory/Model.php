<?php
class Kwf_Component_Cache_Paging_Directory_Model extends Kwf_Model_FnF
{
    protected $_columns = array('id', 'content');
    protected $_data = array(
        array('id' => 1),
        array('id' => 2),
        array('id' => 3),
    );
    protected $_primaryKey = 'id';
}
