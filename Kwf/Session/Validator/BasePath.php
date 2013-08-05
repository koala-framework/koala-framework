<?php
class Kwf_Session_Validator_BasePath extends Kwf_Session_Validator_Abstract
{
    public function setup()
    {
        $this->setValidData( Kwf_Config::getValue('server.baseUrl') );
    }

    public function validate()
    {
        return Kwf_Config::getValue('server.baseUrl') === $this->getValidData();
    }
}
