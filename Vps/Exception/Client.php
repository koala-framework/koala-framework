<?php
class Vps_Exception_Client extends Vps_Exception_NoMail
{
    public function getTemplate()
    {
        return 'Error-client';
    }
}
