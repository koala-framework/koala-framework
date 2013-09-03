<?php
class Kwc_Mail_FullPageCache_TestMail_Model extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected $_data = array(
        array(
            'component_id'=> 'root-testMail-html',
            'subject'     => 'TestSubject',
            'from_email'  => 'noreply@koala-framework.org',
            'from_name'   => 'foo',
            'reply_email' => ''
        )
    );
}
