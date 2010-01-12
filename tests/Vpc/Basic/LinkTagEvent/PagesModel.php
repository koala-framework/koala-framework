<?php
class Vpc_Basic_LinkTagEvent_PagesModel extends Vps_Component_PagesModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(

                // necessary events component
                array('id'=>3100, 'pos'=>1, 'visible'=>true, 'name'=>'EventsBar', 'filename' => 'events1',
                    'parent_id'=>'root', 'component'=>'events', 'is_home'=>false, 'hide'=>false),

                // real link
                array('id'=>6100, 'pos'=>2, 'visible'=>true, 'name'=>'EventLink 1', 'filename' => 'eventlink1',
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

                // dummy
                array('id'=>6200, 'pos'=>3, 'visible'=>true, 'name'=>'EventLink 2', 'filename' => 'eventlink2',
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

            )
        ));
        parent::__construct($config);
    }
}
