<?php
class Kwf_Component_Cache_Fnf_IncludesModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'id';
    protected $_columns = array('id', 'component_id', 'type', 'target_id', 'target_type');
}
