<?php
class Vpc_Mail_Mail_Component extends Vpc_Abstract
{
    public function getMailVars($user = null)
    {
        return array(
            'username' => $user ? $user->getMailLastname() : 'noname'
        );
    }
}
