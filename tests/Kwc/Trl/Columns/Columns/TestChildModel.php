<?php
class Kwc_Trl_Columns_Columns_TestChildModel extends Kwc_Columns_Model
{
    public function __construct(array $config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Trl_Columns_Columns_TestModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id'
        ));
        parent::__construct($config);
    }
}
