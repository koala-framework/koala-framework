<?php
class Kwc_Cards_TestModel extends Kwc_TextImage_ImageEnlarge_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root_cards1', 'component'=>'none'),
            )
        ));
        parent::__construct($config);
    }
}
