<?php
class Kwc_Basic_Text_TestModel extends Kwc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Kwc_Basic_Text_TestChildComponentsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
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
