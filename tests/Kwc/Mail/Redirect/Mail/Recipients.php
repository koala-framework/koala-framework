<?php
class Kwc_Mail_Redirect_Mail_Recipients extends Kwf_Model_FnF
{
    protected $_rowClass = 'Kwc_Mail_Redirect_Mail_Recipient';
    protected $_data = array(
        array(
            'id'=>1, 'gender' => 'male', 'title'=>'Mag.',
            'firstname'=>'Franz', 'lastname'=>'Unger',
            'email' => 'ufx@vivid-planet.com', 'format'=>'html'
        ),
        array(
            'id'=>2, 'gender' => 'female', 'title'=>'',
            'firstname'=>'Alexandra', 'lastname'=>'Rainer',
            'email' => 'ar@vivid-planet.com', 'format'=>'text'
        )
    );
}
