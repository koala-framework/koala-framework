<?php
class Kwc_Mail_FullPageCache_TestMail_Html_Model extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected $_data = array(
        array('component_id'=>'root-testMail1-content', 'content'=>'<p>abcd</p>'),
        array('component_id'=>'root_testMail2-content', 'content'=>'<p>abcd</p>'),
    );
}
