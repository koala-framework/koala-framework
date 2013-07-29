<?php
class Kwf_Session_Validator_RemoteAddr extends Kwf_Session_Validator_Abstract
{
    public function setup()
    {
        $this->setValidData( (isset($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR'] : null) );
    }

    public function validate()
    {
        $currentBrowser = (isset($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR'] : null);

        return $currentBrowser === $this->getValidData();
    }
}
