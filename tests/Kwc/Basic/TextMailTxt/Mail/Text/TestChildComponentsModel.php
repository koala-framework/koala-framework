<?php
class Kwc_Basic_TextMailTxt_Mail_Text_TestChildComponentsModel extends Kwc_Basic_Text_ChildComponentsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Basic_TextMailTxt_Mail_Text_TestModel';
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'component', 'nr', 'saved'),
            'data' => array(
                array('id' => 1, 'component_id'=>'root_mail1-content', 'component'=>'link', 'nr'=>1, 'saved'=>true),
            )
        ));
        parent::__construct($config);
    }
}
