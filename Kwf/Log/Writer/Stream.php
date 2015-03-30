<?php
class Kwf_Log_Writer_Stream extends Zend_Log_Writer_Stream
{
    protected $_mode;
    protected $_url;

    public function __construct($url, $mode = null)
    {
        // Setting the default
        if (null === $mode) {
            $mode = 'a';
        }

        $this->_mode = $mode;
        $this->_url = $url;

        parent::__construct($url, $mode);
        $this->setFormatter(new Zend_Log_Formatter_Simple("%message%\n"));
    }

    protected function _write($event)
    {
        try {
            parent::_write($event);
        } catch (Zend_Log_Exception $e) {
            //if resource is closed (happens during shutdown), re-open
            if (!is_resource($this->_stream)) {
                if (!$this->_stream = @fopen($this->_url, $this->_mode, false)) {
                    throw $e;
                }
                parent::_write($event);
            }
        }
    }
}
