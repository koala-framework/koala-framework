<?php
class Kwf_Session_Validator_HttpHost extends Kwf_Session_Validator_Abstract
{
    public function setup()
    {
        $this->setValidData( (isset($_SERVER['HTTP_HOST'])
            ? $_SERVER['HTTP_HOST'] : null) );
    }

    public function validate()
    {
        $currentBrowser = (isset($_SERVER['HTTP_HOST'])
            ? $_SERVER['HTTP_HOST'] : null);

        return $currentBrowser === $this->getValidData();
    }
}
