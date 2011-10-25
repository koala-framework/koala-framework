<?php
class Kwc_Trl_Text_Text_TestChildComponentsModel extends Kwc_Basic_Text_ChildComponentsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Trl_Text_Text_TestModel';
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'component', 'nr', 'saved'),
            'data' => array(
            )
        ));
        parent::__construct($config);
    }
}
