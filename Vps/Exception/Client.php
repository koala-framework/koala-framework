<?php
class Vps_Exception_Client extends Vps_Exception_NoLog
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
        if (!$ignoreCli && php_sapi_name() == 'cli') {
            file_put_contents('php://stderr', $this->getMessage()."\n");
            exit(1);
        }
        parent::render($ignoreCli);
    }
}
