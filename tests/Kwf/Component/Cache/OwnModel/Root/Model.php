<?php
class Kwf_Component_Cache_OwnModel_Root_Model extends Kwf_Model_FnF
{
    protected $_columns = array('component_id', 'content');
    protected $_primaryKey = 'component_id';
    protected $_data = array(array(
        'component_id' => 'root',
        'content' => 'foo'
    ));
}