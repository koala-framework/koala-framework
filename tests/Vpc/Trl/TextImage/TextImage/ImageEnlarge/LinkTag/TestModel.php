<?php
class Vpc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestModel extends Vpc_TextImage_ImageEnlarge_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test-image-linkTag', 'component'=>'enlarge')
            )
        ));
        parent::__construct($config);
    }
}
