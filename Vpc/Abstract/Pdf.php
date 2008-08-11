<?php
class Vpc_Abstract_Pdf
{
    protected $_component;
    protected $_pdf;
    protected $_indentLeft;
    public function __construct(Vpc_Abstract $component, TCPDF $pdf)
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
}
