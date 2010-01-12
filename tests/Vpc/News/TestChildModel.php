<?php
class Vpc_News_TestChildModel extends Vpc_News_Directory_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 501, 'component_id' => 2100, 'visible' => 1, 'title' => 'a', 'teaser' => 'a-teaser', 'publish_date' => '2010-01-08', 'expiry_date' => null),
                array('id' => 502, 'component_id' => 2100, 'visible' => 1, 'title' => 'b', 'teaser' => 'b-teaser', 'publish_date' => '2010-01-09', 'expiry_date' => null),
                array('id' => 503, 'component_id' => 2100, 'visible' => 1, 'title' => 'c', 'teaser' => 'c-teaser', 'publish_date' => '2010-01-10', 'expiry_date' => null),
            )
        ));
        parent::__construct($config);
    }
}
