<?php
class Kwc_Basic_LinkTagEvent_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(

                // necessary events component
                array('id'=>3100, 'pos'=>1, 'visible'=>true, 'name'=>'EventsBar', 'filename' => 'events1', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'events', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                // real link
                array('id'=>6100, 'pos'=>2, 'visible'=>true, 'name'=>'EventLink 1', 'filename' => 'eventlink1', 'custom_filename' => false,
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                // dummy
                array('id'=>6200, 'pos'=>3, 'visible'=>true, 'name'=>'EventLink 2', 'filename' => 'eventlink2', 'custom_filename' => false,
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

            )
        ));
        parent::__construct($config);
    }
}
