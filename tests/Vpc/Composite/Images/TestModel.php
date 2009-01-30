<?php
class Vpc_Composite_Images_TestModel extends Vpc_Abstract_List_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'component_id', 'visible', 'pos'),
                'data'=> array(
                    array('id'=>1, 'component_id'=>2100, 'visible'=>1, 'pos'=>1),
                    array('id'=>2, 'component_id'=>2100, 'visible'=>1, 'pos'=>2),
                    array('id'=>3, 'component_id'=>2100, 'visible'=>1, 'pos'=>3),
                )
            ));
        parent::__construct($config);
    }
}
