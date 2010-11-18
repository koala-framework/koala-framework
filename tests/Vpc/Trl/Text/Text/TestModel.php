<?php
class Vpc_Trl_Text_Text_TestModel extends Vpc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Vpc_Trl_Text_Text_TestChildComponentsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'content', 'data'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_text', 'content'=>'<p>foo</p>'),
                    array('component_id'=>'root-en_text-child', 'content'=>'<p>fooen</p>')
                )
            ));
        parent::__construct($config);
    }
}
