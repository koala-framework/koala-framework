<?php
class Vpc_Events_TestChildModel extends Vpc_Events_Directory_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 601, 'component_id' => 3100, 'visible' => 1, 'title' => 'a', 'teaser' => 'a-teaser', 'publish_date' => '2010-01-08', 'expiry_date' => null),
                array('id' => 602, 'component_id' => 3100, 'visible' => 1, 'title' => 'b', 'teaser' => 'b-teaser', 'publish_date' => '2010-01-09', 'expiry_date' => null),
                array('id' => 603, 'component_id' => 3100, 'visible' => 1, 'title' => 'c', 'teaser' => 'c-teaser', 'publish_date' => '2010-01-10', 'expiry_date' => null),
            )
        ));
        parent::__construct($config);
    }
}
