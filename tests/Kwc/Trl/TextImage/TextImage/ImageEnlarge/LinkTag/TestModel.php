<?php
class Kwc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestModel extends Kwc_TextImage_ImageEnlarge_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test-image-linkTag', 'component'=>'enlarge')
            )
        ));
        parent::__construct($config);
    }
}
