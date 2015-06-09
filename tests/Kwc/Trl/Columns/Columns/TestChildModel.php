<?php
class Kwc_Trl_Columns_Columns_TestChildModel extends Kwc_Columns_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id'
        ));
        parent::__construct($config);
    }
}
