<?php
class Vpc_Basic_Feed_Model extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'data' =>array(
                array(
                    'id' => 1,
                    'title' => 'testtitle',
                    'description' => 'testdescription',
                    'link' => 'testlink'
                )
            )
        );
        parent::__construct($config);
    }
}
