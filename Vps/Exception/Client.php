<?php
class Vps_Exception_Client extends Vps_ExceptionNoMail {

    public function getTemplate()
    {
        return 'ErrorClient';
    }
}