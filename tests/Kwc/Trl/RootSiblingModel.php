<?php
class Kwc_Trl_RootSiblingModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'id';
    protected $_columns = array('id', 'visible');
    protected $_referenceMap = array(
        'sibling' => array(
            'refModelClass' => 'Kwc_Trl_RootModel',
            'column' => 'id'
        )
    );
}
