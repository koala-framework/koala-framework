<?php
class Vpc_Advanced_Amazon_Nodes_TestNodesModel extends Vpc_Advanced_Amazon_Nodes_NodesModel
{
    public function __construct($config = array())
    {
        $this->_default = array('content'=>'ShouldGetOverwritten');
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'component_id', 'name', 'node_id', 'visible'),
                'primaryKey' => 'id',
                'data'=> array(
                    array('id'=>'1', 'component_id'=>'root_amazon', 'name'=>'Php', 'node_id'=>'166039031', 'visible'=>1),
                    array('id'=>'2', 'component_id'=>'root_amazon', 'name'=>'JavaScript', 'node_id'=>'166035031', 'visible'=>1)
                )
            ));
        parent::__construct($config);
    }
}
