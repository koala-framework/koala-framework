<?php
class Vpc_Basic_LinkTagMail_TestModel extends Vpc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1400', 'mail'=>'example@example.com',
                        'subject' => '', 'text' => ''),
                array('component_id'=>'1401', 'mail'=>'example@example.com',
                        'subject' => 'dere', 'text' => 'hallo'),
                array('component_id'=>'1402', 'mail'=>'',
                        'subject' => '', 'text' => ''),
            )
        ));
        parent::__construct($config);
    }
}
