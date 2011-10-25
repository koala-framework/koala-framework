<?php
class Kwc_Basic_TextSessionModel_TestChildComponentsModel extends Kwc_Basic_Text_ChildComponentsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Basic_TextSessionModel_TestModel';
        $config['proxyModel'] = new Kwf_Model_Session(array(
            'namespace' => 'TextSessionModel_TestChildComponentsModel',
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'component', 'nr', 'saved'),
        ));
        parent::__construct($config);
    }
}
