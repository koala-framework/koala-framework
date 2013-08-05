<?php
class Kwf_Session_Validator_BasePath extends Kwf_Session_Validator_Abstract
{
    public function setup()
    {
        $this->setValidData( Kwf_Setup::getBaseUrl() );
    }

    public function validate()
    {
        return Kwf_Setup::getBaseUrl() === $this->getValidData();
    }
}
