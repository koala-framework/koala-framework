<?php
class Vpc_Basic_Text_TestModel extends Vpc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Vpc_Basic_Text_TestChildComponentsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'default' => array('content'=>'ShouldGetOverwritten'),
                'columns' => array('component_id', 'content', 'data'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'1000', 'content'=>'<p>foo</p>'),
                    array('component_id'=>'1003', 'content'=>'')
                )
            ));
        parent::__construct($config);
    }
}
