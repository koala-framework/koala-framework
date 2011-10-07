<?php
class Kwc_Mail_Placeholder_Content_Component extends Kwc_Abstract
{
    public function getMailVars($user = null)
    {
        return array(
            'username' => $user ? $user->getMailLastname() : 'noname'
        );
    }
}
