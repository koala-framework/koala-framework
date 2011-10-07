<?php
class Kwc_Basic_TextConvertLinkOnlyExtern_TestChildComponentsModel extends Kwc_Basic_Text_ChildComponentsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Basic_TextConvertLinkOnlyExtern_TestModel';
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'component', 'nr', 'saved'),
            'data' => array(
                array('id' => 4, 'component_id'=>'1007', 'component'=>'link', 'nr'=>1, 'saved'=>true),
            )
        ));
        parent::__construct($config);
    }
}
