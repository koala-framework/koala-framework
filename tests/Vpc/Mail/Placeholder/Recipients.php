<?php
class Vpc_Mail_Placeholder_Recipients extends Vps_Model_FnF
{
    protected $_rowClass = 'Vpc_Mail_Placeholder_Recipient';
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