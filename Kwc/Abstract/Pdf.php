<?php
class Kwc_Abstract_Pdf
{
    protected $_component;
    protected $_pdf;
    protected $_indentLeft;
    public function __construct(Kwc_Abstract $component, TCPDF $pdf)
    {
        $this->_component = $component;
        $this->_pdf = $pdf;
    }
    public function writeContent()
    {
    }
    public function getPdf()
    {
        return $this->_pdf;
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->_pdf, $method)) {
            return call_user_func_array(array($this->_pdf, $method), $arguments);
        } else {
            throw new Kwf_Exception("Invalid method called: '$method'");
        }
    }

}
