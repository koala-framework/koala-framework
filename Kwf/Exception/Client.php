<?php
class Kwf_Exception_Client extends Kwf_Exception_NoLog
{
    public function getHeader()
    {
        return 'HTTP/1.1 200 OK';
    }

    public function getTemplate()
    {
        return 'ErrorClient';
    }

    
    public function render($ignoreCli = false)
    {
        if (!$ignoreCli && PHP_SAPI == 'cli') {
            file_put_contents('php://stderr', $this->getMessage()."\n");
            exit(1);
        }
        parent::render($ignoreCli);
    }
}
