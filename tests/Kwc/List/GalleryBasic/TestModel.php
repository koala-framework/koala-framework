<?php
class Kwc_List_GalleryBasic_TestModel extends Kwc_Abstract_List_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('id', 'component_id', 'visible', 'pos', 'data'),
                'data'=> array(
                    array('id'=>1, 'component_id'=>'root_page1', 'visible'=>1, 'pos'=>1),
                    array('id'=>2, 'component_id'=>'root_page1', 'visible'=>1, 'pos'=>2),
                    array('id'=>3, 'component_id'=>'root_page1', 'visible'=>1, 'pos'=>3),
                )
            ));
        parent::__construct($config);
    }
}
