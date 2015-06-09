<?php
class Kwc_Basic_Text_TestChildComponentsModel extends Kwc_Basic_Text_ChildComponentsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Basic_Text_TestModel';
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'component', 'nr', 'saved'),
            'data' => array(
                array('id' => 1, 'component_id'=>'1000', 'component'=>'link', 'nr'=>3, 'saved'=>true),
                array('id' => 2, 'component_id'=>'1000', 'component'=>'link', 'nr'=>1, 'saved'=>true),
                array('id' => 3, 'component_id'=>'1000', 'component'=>'link', 'nr'=>2, 'saved'=>true),
                array('id' => 4, 'component_id'=>'1007', 'component'=>'link', 'nr'=>1, 'saved'=>true),
                array('id' => 5, 'component_id'=>'1013', 'component'=>'download', 'nr'=>1, 'saved'=>true),
                array('id' => 6, 'component_id'=>'1015', 'component'=>'image', 'nr'=>1, 'saved'=>true),
                array('id' => 7, 'component_id'=>'1011', 'component'=>'image', 'nr'=>1, 'saved'=>true),
            )
        ));
        parent::__construct($config);
    }
}
